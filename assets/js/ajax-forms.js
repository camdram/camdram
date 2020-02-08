import Camdram from './base.js';
import Sortable from 'sortablejs/modular/sortable.core.esm.js';

const CLASS_ROLE     = 'editable-role-role';
const CLASS_PERSON   = 'editable-role-person';

/**
 * A method to make an Ajax call while keeping a FontAwesome icon updated
 * with the progress and any errors.
 *
 * Sends data as x-www-form-urlencoded.
 */
function doAjaxWithIcon(method, url, status_icon, data, on_success) {
    let xhr = new XMLHttpRequest();
    status_icon.classList.add('fa-spinner');
    status_icon.classList.add('fa-spin');
    status_icon.classList.remove('fa-exclamation-circle');
    status_icon.title = 'Loading...';
    xhr.addEventListener("load", function() {
        if (xhr.status >= 200 && xhr.status < 300) {
            status_icon.classList.remove('fa-spinner');
            status_icon.classList.remove('fa-spin');
            status_icon.classList.remove('fa-exclamation-circle');
            status_icon.title = '';

            on_success(xhr);
        } else {
            status_icon.classList.remove('fa-spinner');
            status_icon.classList.remove('fa-spin');
            status_icon.classList.add('fa-exclamation-circle');
            status_icon.title = 'Unable to save changes (error ' + xhr.status + '). Try refreshing the page.';
        }
    });
    xhr.addEventListener("error", function() {
        status_icon.classList.remove('fa-spinner');
        status_icon.classList.remove('fa-spin');
        status_icon.classList.add('fa-exclamation-circle');
        status_icon.title = 'Unable to save changes. Try refreshing the page.';
    });
    let body = '';
    for (const key of Object.keys(data)) {
        if (Array.isArray(data[key])) {
            for (const el of data[key]) {
                body += `&${key}[]=${encodeURIComponent(el)}`;
            }
        } else {
            body += `&${key}=${encodeURIComponent(data[key])}`;
        }
    }
    body = body.substring(1);
    xhr.open(method, url);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.send(body);
}

// Role editor
/**
 * Takes data object
 * { role: <string>,
 *   person: { slug: <string>, name: <string> },
 *   id: <int> }
 */
function buildrow(data) {
  let row = document.createElement('div');
  row.className = 'editable-role';
  if (data) row.dataset.roleId = data.id;

  row.insertAdjacentHTML('beforeend', `<span class="${CLASS_ROLE}"></span>`);
  if (data) row.lastChild.innerText = data.role;
  row.insertAdjacentHTML('beforeend', `<input class="${CLASS_ROLE}" placeholder="Role, e.g. Director, Romeo" type="text">`);
  row.lastChild.style.display = 'none';

  row.insertAdjacentHTML('beforeend', `<a itemprop="url" href="" class="${CLASS_PERSON}"><span></span></a>`);
  if (data) {
    row.lastChild.href = Routing.generate('get_person', {"identifier": data.person.slug});
    row.lastChild.firstChild.innerText = data.person.name;
  }
  row.insertAdjacentHTML('beforeend', `<input class="${CLASS_PERSON}" placeholder="Name" type="text">`);
  row.lastChild.style.display = 'none';

  row.insertAdjacentHTML('beforeend', '<button class="fa fa-pencil tiny-button" title="Edit role"></button>');
  row.insertAdjacentHTML('beforeend', '<button class="fa fa-trash-o tiny-button" title="Delete role"></button>');
  row.insertAdjacentHTML('beforeend', '<button class="fa fa-undo tiny-button" title="Undo"></button>');
  row.insertAdjacentHTML('beforeend', '<button class="fa fa-check tiny-button" title="Confirm"></button>');
  row_setbuttons(row, {
    'fa-pencil': open_editor_e,
    'fa-trash-o': delete_role_e
  });
  row.insertAdjacentHTML('beforeend', '<i class="fa roles-status-icon"></i>');

  row.insertAdjacentHTML('beforeend', '<button class="text-button new-role-button"><span>Add new role</span></button>');
  row.lastChild.onclick = add_row_e;

  return row;
}

function open_editor(row) {
  let role_span = row.querySelector(`span.${CLASS_ROLE}`);
  let person_a  = row.querySelector(`a.${CLASS_PERSON}`);
  let role_field   = row.querySelector(`input.${CLASS_ROLE}`);
  let person_field = row.querySelector(`input.${CLASS_PERSON}`);
  role_field.value   = role_span.innerText;
  person_field.value = person_a.innerText;

  role_span.style.display = person_a.style.display = 'none';
  role_field.style.display = person_field.style.display = null;

  row.classList.add('unsortable');

  row_setbuttons(row, {
    'fa-undo': close_editor_nosave_e,
    'fa-check': close_editor_save_e
  });
}
function close_editor(row, saveChanges) {
  let role_span = row.querySelector(`span.${CLASS_ROLE}`);
  let person_a  = row.querySelector(`a.${CLASS_PERSON}`);
  let role_field   = row.querySelector(`input.${CLASS_ROLE}`);
  let person_field = row.querySelector(`input.${CLASS_PERSON}`);

  row.classList.remove('unsortable');

  if (saveChanges) {
    let icon = row.querySelector('.roles-status-icon');
    let id = row.dataset.roleId === undefined ? 'new' : parseInt(row.dataset.roleId);
    let show_slug = row.parentNode.dataset.showSlug;
    let role_type = row.parentNode.dataset.roleType;

    doAjaxWithIcon("PATCH",
        Routing.generate('patch_show_role', {identifier: show_slug}),
        icon, {
            'id': id, 'role': role_field.value, 'role_type': role_type,
            'person': person_field.value,
            '_token': row.parentNode.dataset.csrfPatch
        }, (xhr) => {
            let response = JSON.parse(xhr.response);
            if (row.dataset.roleId === undefined) {
                row.dataset.roleId = response.id;
            }
            person_a.href = Routing.generate("get_person", {identifier: response.person_slug});

            row_save_order(row);
    });

    role_span.innerText = role_field.value;
    person_a.innerText  = person_field.value;
  }

  role_field.style.display = person_field.style.display = 'none';
  role_span.style.display = person_a.style.display = null;

  row_setbuttons(row, {
    'fa-pencil': open_editor_e,
    'fa-trash-o': delete_role_e
  });
}
function set_role_deleted(row, willDelete) {
    if (willDelete) {
        // Don't allow user to do anything until the Ajax completes as this could
        // lead to errors.
        row_setbuttons(row, {});

        doAjaxWithIcon("DELETE", Routing.generate("delete_show_role"),
            row.querySelector('.roles-status-icon'), {
                '_token': row.parentNode.dataset.csrfDelete,
                'role': row.dataset.roleId
            }, function() {
                row.classList.add('deleted');

                row_setbuttons(row, {
                    'fa-undo': undelete_role_e
                });
            });

        delete row.dataset.roleId;
    } else {
        let icon = row.querySelector('.roles-status-icon');
        let show_slug = row.parentNode.dataset.showSlug;
        let role_type = row.parentNode.dataset.roleType;
        let role_span = row.querySelector(`span.${CLASS_ROLE}`);
        let person_a  = row.querySelector(`a.${CLASS_PERSON}`);

        row_setbuttons(row, {});

        doAjaxWithIcon("PATCH",
            Routing.generate('patch_show_role', {identifier: show_slug}),
            icon, {
                'id': 'new', 'role': role_span.innerText, 'role_type': role_type,
                'person': person_a.innerText,
                '_token': row.parentNode.dataset.csrfPatch
            }, (xhr) => {
                let response = JSON.parse(xhr.response);
                row.dataset.roleId = response.id;
                person_a.href = Routing.generate("get_person", {identifier: response.person_slug});
                row.classList.remove('deleted');

                row_setbuttons(row, {
                  'fa-pencil': open_editor_e,
                  'fa-trash-o': delete_role_e
                });

                row_save_order(row);
            });
    }
}
function delete_row(row) {
  row.parentNode.removeChild(row);
}

function add_row(row_or_button, isAtBottom) {
  let parent = isAtBottom ? row_or_button.previousElementSibling : row_or_button.parentNode;
  let newRow = buildrow(null);
  parent.insertBefore(newRow, isAtBottom ? null : row_or_button);
  open_editor(newRow);
  row_setbuttons(newRow, {
    'fa-check': close_editor_save_e,
    'fa-trash-o': delete_row_e
  });
}

var close_editor_save_e = make_evt_handler(close_editor, true);
var close_editor_nosave_e = make_evt_handler(close_editor, false);
var delete_row_e = make_evt_handler(delete_row);
var delete_role_e = make_evt_handler(set_role_deleted, true);
var undelete_role_e = make_evt_handler(set_role_deleted, false);
var open_editor_e = make_evt_handler(open_editor);
var add_row_e = make_evt_handler(add_row, false);
var add_row_bottom_e = make_evt_handler(add_row, true);

function make_evt_handler(fn_on_row, ...args) {
  return function(event) {
    fn_on_row(event.currentTarget.parentNode, ...args);
  };
}

/**
 * Set handlers for buttons and hide all buttons not relevant.
 * handers: an object mapping class names to functions
 * Note that row may *not* be part of the DOM tree so querySelector* is
 * unavailable; therefore walking the DOM old-style.
 * This function uses the ele.on* event handlers to automatically clear the
 * previous handler if relevant.
 */
function row_setbuttons(row, handlers) {
  outerloop:
  for (let ele of row.children) {
    if (ele.tagName.toLowerCase() !== 'button' || !ele.classList.contains('fa')) continue;
    for (let className in handlers) {
      if (ele.classList.contains(className)) {
        ele.onclick = handlers[className];
        ele.style.display = null;
        continue outerloop;
      }
    }
    // if unmatched by any handler:
    ele.onclick = null;
    ele.style.display = 'none';
  }
}

function row_save_order(row) {
    let roles = [];
    for (let i = 0; i < row.parentNode.children.length; i++) {
        let roleId = row.parentNode.children[i].dataset.roleId;
        if (roleId !== undefined) roles.push(roleId);
    }
    doAjaxWithIcon('PATCH', Routing.generate('patch_roles_reorder'),
        row.querySelector('.roles-status-icon'),
        { 'role': roles }, xhr => {
            let response = (xhr.responseType === 'json') ?
                xhr.response : JSON.parse(xhr.response);
            if (response.failures) {
                Camdram.flashMessage('error', 'An error occured when re-ordering roles. Try refreshing the page.');
            }
        });
}

window.addEventListener('DOMContentLoaded', (e) => {
    for (const row of document.querySelectorAll('.editable-role')) {
        row.parentNode.replaceChild(
            buildrow(JSON.parse(row.dataset.roleJson)), row);
    }

    for (const container of document.querySelectorAll('.editable-role-container')) {
        container.insertAdjacentHTML('afterend',
            '<div class="text-center" style="margin-top: -17px; z-index: 5; position: relative;"><button class="text-button new-role-button"><span>Add new role</span></button></div>');
        container.nextElementSibling.lastChild.addEventListener("click", add_row_bottom_e);
        let sortable = Sortable.create(container, {
            filter: '.unsortable',
            preventOnFilter: false,
            onSort: (evt) => {
                row_save_order(evt.item);
            }
        });
    }
});

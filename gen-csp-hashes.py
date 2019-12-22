#!/usr/bin/env python3

import argparse
import base64
import hashlib
import os
import re
import sys

# These tags use {#- -#} to strip whitespace in the generated HTML, and so the
# strip() method is used correspondingly here.
# Non-greedy matching used to allow multiple script tags per file.
regex = re.compile('(?<={#- begin-CSP-permitted-script -#}).*?(?={#- end-CSP-permitted-script -#})', flags=re.DOTALL | re.MULTILINE)
sha256 = hashlib.sha256()

parser = argparse.ArgumentParser(description='Generate part of CSP header from app/Resources/views.')
parser.add_argument('--debug-mode', action='store_true', help='Print lots of info (use a pager!)')
parser.add_argument('--set-unsafe', action='store_true', help='Allow any inline JS, don\'t generate hashes.')
args = parser.parse_args()

with open('web/build/csp_hashes.txt', 'w', encoding='utf-8', newline='') as outfile:
    if args.set_unsafe:
        print("'unsafe-inline'", end='', file=outfile)
        sys.exit(0)
    for root, dirs, files in os.walk('app/Resources/views'):
        for name in files:
            try:
                with open(os.path.join(root, name), encoding='utf-8', newline='') as f:
                    content = f.read()
                for match in regex.findall(content):
                    match = bytes(match.strip(), 'utf-8')
                    if args.debug_mode:
                        print(root + name, base64.b64encode(hashlib.sha256(match).digest()).decode('utf-8'))
                        print(match.decode('utf-8'))
                        print()
                    print('\'sha256-' + base64.b64encode(hashlib.sha256(match).digest()).decode('utf-8'), end='\' ', file=outfile)
            except Exception as e:
                print(f"WARNING: Failed to process {root}/{name}");
                print(e);


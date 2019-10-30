#!/usr/bin/env python3

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

debug_mode = (len(sys.argv) > 1) and ('--debug-mode' in sys.argv)

with open('web/build/csp_hashes.txt', 'w', encoding='utf-8', newline='') as outfile:
    for root, dirs, files in os.walk('app/Resources/views'):
        for name in files:
            with open(os.path.join(root, name), encoding='utf-8', newline='') as f:
                content = f.read()
                for match in regex.findall(content):
                    match = bytes(match.strip(), 'utf-8')
                    if debug_mode:
                        print(root + name, base64.b64encode(hashlib.sha256(match).digest()).decode('utf-8'))
                        print(match.decode('utf-8'))
                        print()
                    print('\'sha256-' + base64.b64encode(hashlib.sha256(match).digest()).decode('utf-8'), end='\' ', file=outfile)


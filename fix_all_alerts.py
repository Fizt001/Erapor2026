import os
import re

dir_path = r'd:\koding\Erapor2026\erapor-fe\app\pages'

def replace_alerts(content):
    content = re.sub(r"alert\('([^']+)'\)", r"displayToast('\1', 'error')", content)
    content = re.sub(r"alert\((error\.response\._data\.message|error\.message)\)", r"displayToast(\1, 'error')", content)
    return content

for root, _, files in os.walk(dir_path):
    for file in files:
        if file.endswith('.vue'):
            filepath = os.path.join(root, file)
            with open(filepath, 'r', encoding='utf-8') as f:
                content = f.read()
            
            new_content = replace_alerts(content)
            if new_content != content:
                with open(filepath, 'w', encoding='utf-8') as f:
                    f.write(new_content)
                print(f'Updated alerts in {filepath}')

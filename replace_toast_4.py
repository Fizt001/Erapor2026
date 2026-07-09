import os
import re

dir_path = r"d:\koding\Erapor2026\erapor-fe\app\pages"

def extract_and_replace_toast(content):
    # Find displayToast function
    func_start = content.find("const displayToast = (msg")
    if func_start == -1:
        func_start = content.find("const displayToast = (message")
    
    if func_start != -1:
        # find the end of the arrow function
        open_brackets = 0
        in_func = False
        func_end = -1
        for i in range(func_start, len(content)):
            if content[i] == '{':
                open_brackets += 1
                in_func = True
            elif content[i] == '}':
                open_brackets -= 1
                if open_brackets == 0 and in_func:
                    func_end = i
                    break
        
        if func_end != -1:
            original_func = content[func_start:func_end+1]
            new_func = """const displayToast = (msg, type = 'success') => {
    useSwal().fire({
        title: msg,
        icon: type === 'error' ? 'error' : 'success',
        confirmButtonText: 'OK',
        confirmButtonColor: '#4f46e5'
    })
}"""
            content = content[:func_start] + new_func + content[func_end+1:]
    
    return content

for root, _, files in os.walk(dir_path):
    for file in files:
        if file.endswith('.vue'):
            filepath = os.path.join(root, file)
            with open(filepath, 'r', encoding='utf-8') as f:
                content = f.read()
            
            if 'displayToast' in content:
                new_content = extract_and_replace_toast(content)
                if new_content != content:
                    with open(filepath, 'w', encoding='utf-8') as f:
                        f.write(new_content)
                    print(f"Updated {filepath}")

print("Done!")

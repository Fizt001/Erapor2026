import os
import re

dir_path = r"d:\koding\Erapor2026\erapor-fe\app\pages"

html_regex = re.compile(r"<!-- Toast Notification -->.*?</div>\s*</div>\s*</template>", re.DOTALL)
script_regex = re.compile(r"const showToast = ref\(false\)\s*const toastMessage = ref\(''\)\s*const toastType = ref\('.*?'\)\s*const displayToast = \([^\)]*\) => \{.*?(?:showToast\.value = true.*?setTimeout.*?3500\).*?|.*?)\}", re.DOTALL)

def process_file(filepath):
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()

    if 'displayToast' not in content:
        return

    # Replace HTML block
    new_content = html_regex.sub("</div>\n</template>", content)
    
    # Replace Script block
    new_script = """const displayToast = (msg, type = 'success') => {
    useSwal().fire({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        icon: type === 'error' ? 'error' : 'success',
        title: msg
    })
}"""
    new_content = script_regex.sub(new_script, new_content)

    if new_content != content:
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(new_content)
        print(f"Updated {filepath}")

for root, _, files in os.walk(dir_path):
    for file in files:
        if file.endswith('.vue'):
            process_file(os.path.join(root, file))

print("Done!")

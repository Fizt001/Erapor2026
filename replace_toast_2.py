import os
import re

dir_path = r"d:\koding\Erapor2026\erapor-fe\app\pages"

def process_file(filepath):
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()

    if 'displayToast' not in content:
        return

    # Replace HTML block
    # It starts with <!-- Toast Notification --> and ends with </div>
    # The end of the block is tricky because of nested divs.
    # It's usually the last thing before </template>
    html_pattern = re.compile(r"<!-- Toast Notification -->.*?</template>", re.DOTALL)
    new_content = html_pattern.sub("</template>", content)
    
    # Replace the displayToast function definition
    # This matches const displayToast = ... => { ... }
    # Since we can't reliably parse JS with regex, we'll match up to the end of the block.
    # We will just replace everything from const showToast to the end of displayToast function.
    
    script_pattern = re.compile(r"const showToast\s*=\s*ref\(false\).*?const displayToast\s*=\s*\([^)]*\)\s*=>\s*\{.*?(?:setTimeout.*?\}.*?\}|showToast\.value\s*=\s*true\s*\})", re.DOTALL)
    
    # Alternatively, just replace the body of displayToast
    body_pattern = re.compile(r"(const displayToast\s*=\s*\([^)]*\)\s*=>\s*\{).*?(\n\s*setTimeout[^\n]*\n\s*\})", re.DOTALL)
    
    # Let's use a simpler approach: replace the entire showToast, toastMessage, toastType refs and displayToast function.
    simple_pattern = re.compile(r"const showToast.*?setTimeout.*?\}", re.DOTALL)
    
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

    new_content = simple_pattern.sub(new_script, new_content)

    if new_content != content:
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(new_content)
        print(f"Updated {filepath}")

for root, _, files in os.walk(dir_path):
    for file in files:
        if file.endswith('.vue'):
            process_file(os.path.join(root, file))

print("Done!")

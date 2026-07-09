import re
import io

def fix():
    with open('d:/koding/Erapor2026/erapor-fe/app/pages/admin/kenaikan-kelas.vue', 'rb') as f:
        content = f.read()
    
    if content.startswith(b'\xff\xfe') or b'\x00' in content[:100]:
        text = content.decode('utf-16', 'ignore')
    else:
        text = content.decode('utf-8', 'ignore')
    
    replacements = {
        'dYZ\"': '🎓',
        'dY\"?': '⚠️',
        'dYsO': '🚶',
        'dYsª': '🚪',
        'dY\"„': '🔄',
        'âœ…': '✅',
        'âš ï¸': '⚠️',
        'â ³': '⏳',
        'ðŸ’¾': '💾',
        'ðŸŽ“': '🎓',
        'ðŸ” ': '⚠️',
        'ðŸšŒ': '🚶',
        'ðŸšª': '🚪',
        'ðŸ”„': '🔄',
        'ðŸ «': '🏫',
        'ðŸª¹': '📋',
        'o. Naik': '✅ Naik',
        'dY?': '🏫',
        'dY1': '📋',
        'A,A?A': '🏫'
    }
    
    for k, v in replacements.items():
        text = text.replace(k, v)

    with open('d:/koding/Erapor2026/erapor-fe/app/pages/admin/kenaikan-kelas.vue', 'w', encoding='utf-8') as f:
        f.write(text)

if __name__ == '__main__':
    fix()
    print("Done")

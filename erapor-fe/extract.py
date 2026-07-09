import re
with open('buku_induk_readable.txt', 'r', encoding='utf-8') as f:
    code = f.read()

idx = code.find('Cetak A0')
print("Found Cetak A0 at index:", idx)
if idx != -1:
    print(code[max(0, idx-5000):min(len(code), idx+8000)])

import os
import re

dir_path = r"d:\koding\Erapor2026\erapor-fe\app\layouts"

for file in os.listdir(dir_path):
    if file.endswith('.vue'):
        filepath = os.path.join(dir_path, file)
        with open(filepath, 'r', encoding='utf-8') as f:
            content = f.read()

        # Update aside class
        # Look for <aside ... :class="{ ... }"> or similar
        # Since each has different initial width (w-64 or w-56), we'll replace the whole aside tag
        aside_pattern = re.compile(r'<aside class="([^"]*)"\s*:class="([^"]*)">')
        
        # We also need to add group to the class if not exists
        def aside_replacer(match):
            cls = match.group(1)
            bnd = match.group(2)
            
            # Remove existing width classes
            cls = re.sub(r'\bw-\d+\b', '', cls)
            # Remove transform related stuff if we are overriding
            cls = re.sub(r'\btransition-transform\b', '', cls)
            if 'group' not in cls:
                cls = 'group ' + cls
            
            cls = re.sub(r'\s+', ' ', cls).strip()
            cls += " transition-all duration-300 ease-in-out transform overflow-x-hidden"
            
            # new bind class
            # standard: [sidebarOpen ? 'translate-x-0 w-64' : '-translate-x-full lg:translate-x-0 lg:w-[72px] lg:hover:w-64 w-64']
            new_bnd = "[sidebarOpen ? 'translate-x-0 w-64' : '-translate-x-full lg:translate-x-0 lg:w-[72px] lg:hover:w-64 w-64']"
            
            return f'<aside class="{cls}" :class="{new_bnd}">'
        
        content = aside_pattern.sub(aside_replacer, content)

        # Update NuxtLinks
        # <NuxtLink ... class="... transition-colors" ...>
        #   <span class="mr-3 text-lg">...</span> Text
        # </NuxtLink>
        # We need to wrap the Text inside a span with opacity transition.
        def link_replacer(match):
            link_start = match.group(1)
            icon_span = match.group(2)
            text = match.group(3).strip()
            
            # Add whitespace-nowrap to link if not there
            if 'whitespace-nowrap' not in link_start:
                link_start = link_start.replace('transition-colors"', 'transition-colors whitespace-nowrap"')
                
            return f'{link_start}\n{icon_span}\n            <span class="opacity-100 lg:opacity-0 lg:group-hover:opacity-100 transition-opacity duration-300">{text}</span>'
        
        # Regex to find NuxtLinks with an icon span
        link_pattern = re.compile(r'(<NuxtLink[^>]*>)\s*(<span[^>]*>.*?</span>)\s*([^<]+)(?=\s*</NuxtLink>)', re.DOTALL)
        content = link_pattern.sub(link_replacer, content)

        # Update Section Titles
        # <div class="pt-4 ... uppercase tracking-widest">
        #   Text
        # </div>
        # Find divs that have tracking-widest and add whitespace-nowrap opacity classes
        def title_replacer(match):
            div_start = match.group(1)
            inner = match.group(2)
            if 'opacity' not in div_start:
                div_start = div_start.replace('tracking-widest"', 'tracking-widest whitespace-nowrap opacity-100 lg:opacity-0 lg:group-hover:opacity-100 transition-opacity duration-300"')
            return f'{div_start}{inner}'
        
        title_pattern = re.compile(r'(<div[^>]*tracking-widest[^>]*>)(.*?</div>)', re.DOTALL)
        content = title_pattern.sub(title_replacer, content)
        
        # Update Header
        # <div class="h-14 ... border-slate-800">
        #   <span class="... mr-2">e</span>-Rapor
        # </div>
        # It's better to manually replace the header content since it varies slightly.
        # But we can find the h-14 div inside the aside.
        def header_replacer(match):
            div_start = match.group(1)
            inner = match.group(2)
            if 'whitespace-nowrap' not in div_start:
                div_start = div_start.replace('border-slate-800"', 'border-slate-800 whitespace-nowrap overflow-hidden"')
            
            # wrap '-Rapor' in span
            if '-Rapor' in inner and '<span class="opacity-100' not in inner:
                inner = inner.replace('-Rapor', '<span class="opacity-100 lg:opacity-0 lg:group-hover:opacity-100 transition-opacity duration-300">-Rapor</span>')
            
            # add ml-1 to 'e' to center it slightly
            inner = inner.replace('mr-2">e<', 'mr-2 ml-1 text-xl">e<')
            
            # for guru/siswa badges
            inner = re.sub(r'(<span[^>]*bg-[a-z]+-500/20[^>]*>)(.*?</span>)', r'\1\2', inner)
            # Actually, to hide badges:
            inner = re.sub(r'(<span[^>]*bg-[a-z]+-500/20[^>]*)(">)', r'\1 opacity-100 lg:opacity-0 lg:group-hover:opacity-100 transition-opacity duration-300\2', inner)
            
            return f'{div_start}{inner}'
        
        header_pattern = re.compile(r'(<div class="h-14 flex items-center px-4[^>]*>)(.*?</div\s*>)', re.DOTALL)
        # Apply only to the first match
        content = header_pattern.sub(header_replacer, content, count=1)

        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(content)
            print(f"Updated {filepath}")

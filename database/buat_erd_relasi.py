from pathlib import Path
from html import escape
import sqlite3
from PIL import Image, ImageDraw, ImageFont

ROOT = Path(__file__).resolve().parent
conn = sqlite3.connect((ROOT / 'database.sqlite').as_uri() + '?mode=ro', uri=True)
W, H = 1570, 2300
img = Image.new('RGB', (W, H), 'white')
draw = ImageDraw.Draw(img)
svg = [f'<svg xmlns="http://www.w3.org/2000/svg" width="{W}" height="{H}" viewBox="0 0 {W} {H}">', '<rect width="100%" height="100%" fill="white"/>']
FONT = Path('C:/Windows/Fonts')

def text(x, y, value, size=20, color='#182637', bold=False):
    font = ImageFont.truetype(str(FONT / ('arialbd.ttf' if bold else 'arial.ttf')), size)
    draw.text((x, y), value, font=font, fill=color)
    svg.append(f'<text x="{x}" y="{y + size}" font-family="Arial, sans-serif" font-size="{size}" fill="{color}" font-weight="{"bold" if bold else "normal"}">{escape(value)}</text>')

def rect(x, y, w, h, fill, outline=None):
    draw.rectangle((x, y, x+w, y+h), fill=fill, outline=outline, width=2)
    svg.append(f'<rect x="{x}" y="{y}" width="{w}" height="{h}" fill="{fill}" stroke="{outline or "none"}" stroke-width="2"/>')

def line(points, color='#36649a', dashed=False, width=3):
    if dashed:
        for a, b in zip(points, points[1:]):
            dx, dy = b[0]-a[0], b[1]-a[1]
            length = (dx*dx+dy*dy)**0.5
            for start in range(0, int(length), 15):
                end = min(start+8, length)
                draw.line((a[0]+dx*start/length, a[1]+dy*start/length, a[0]+dx*end/length, a[1]+dy*end/length), fill=color, width=width)
    else:
        draw.line(points, fill=color, width=width)
    coords = ' '.join(f'{x},{y}' for x,y in points)
    dash = ' stroke-dasharray="8 7"' if dashed else ''
    svg.append(f'<polyline points="{coords}" fill="none" stroke="{color}" stroke-width="{width}"{dash}/>')

def table(name, x, y, color='#244e77'):
    columns = conn.execute(f'PRAGMA table_info("{name}")').fetchall()
    unique = set()
    for index in conn.execute(f'PRAGMA index_list("{name}")').fetchall():
        if index[2]:
            cols = conn.execute(f'PRAGMA index_info("{index[1]}")').fetchall()
            if len(cols) == 1:
                unique.add(cols[0][2])
    rect(x, y, 410, 48+34*len(columns), '#ffffff', '#9eacba')
    rect(x, y, 410, 48, color)
    text(x+16, y+10, name, 23, '#ffffff', True)
    for i, (_, col, typ, required, default, pk) in enumerate(columns):
        yy = y+48+34*i
        if i%2 == 0:
            rect(x+2, yy, 406, 34, '#f3f6f9')
        marker = 'PK' if pk else ('UK' if col in unique else ('LR' if (name,col) in [('sessions','user_id'),('password_reset_tokens','email')] else ''))
        text(x+12, yy+6, marker, 17, '#315d88', True)
        text(x+52, yy+5, col, 18, bold=bool(pk))
        label = typ.lower()
        label = {'varchar':'str', 'integer':'int','datetime':'datetime'}.get(label, label)
        if not required and not pk:
            label += '?'
        text(x+302, yy+6, label, 16, '#5d6d7c')

text(40, 24, 'ERD Sistem Informasi Pengelolaan Administrasi Surat', 34, bold=True)
text(40, 77, 'Skema aktual SQLite | 11 tabel | Tidak ada constraint foreign key', 23, '#5d6d7c')
text(40, 118, 'PK: primary key   UK: unique key   LR: referensi logis (bukan FK)   ?: nullable', 20)
text(40, 155, 'A. Autentikasi dan sesi pengguna', 23, bold=True)

line([(450,279),(510,279),(510,245),(580,245)], dashed=True)
text(460, 295, '0..N', 18, '#36649a', True)
text(526, 216, '0..1', 18, '#36649a', True)
line([(990,313),(1050,313),(1050,245),(1120,245)], '#986b2d', True)
text(1005, 330, '1', 18, '#986b2d', True)
text(1065, 216, '0..1', 18, '#986b2d', True)
table('sessions',40,180)
table('users',580,180)
table('password_reset_tokens',1120,180)
line([(40,525),(110,525)], dashed=True)
text(125,510,'Relasi sesi aktif: sessions.user_id mengacu ke users.id.',20)
line([(40,565),(110,565)], '#986b2d', True)
text(125,550,'Relasi token melalui email disediakan Laravel; fitur reset kata sandi belum digunakan aplikasi.',20)

text(40, 610, 'B. Data surat', 23, bold=True)
table('surat_masuk',250,660)
table('surat_keluar',910,660)
text(250, 1200, 'Kedua tabel surat tidak memiliki kolom penghubung ke users maupun ke satu sama lain.',20)
text(250, 1237, 'Disposisi, asal, pengirim, dan penerima berupa teks; metadata lampiran berada di tabel surat.',20)

text(40, 1300, 'C. Tabel pendukung framework (tanpa relasi antartabel yang diterapkan aplikasi)',23,bold=True)
table('cache',40,1350,'#586777')
table('cache_locks',580,1350,'#586777')
table('jobs',1120,1350,'#586777')
table('migrations',40,1730,'#586777')
table('failed_jobs',580,1730,'#586777')
table('job_batches',1120,1730,'#586777')
text(40, 2200, 'Garis putus-putus menunjukkan hubungan logis; database belum menjamin integritas referensial hubungan tersebut.',20)
text(40, 2240, 'Kardinalitas mengikuti maksud penggunaan aplikasi. Relasi pengguna-pembuat surat dan balasan surat belum diimplementasikan.',18,'#5d6d7c')
svg.append('</svg>')
(ROOT / 'ERD-relasi-logis.svg').write_text('\n'.join(svg), encoding='utf-8')
img.save(ROOT / 'ERD-relasi-logis.png')

mermaid = ['erDiagram', '    users |o..o{ sessions : "sesi login (logis, tanpa FK)"', '    users ||..o| password_reset_tokens : "email (mekanisme reset belum aktif)"']
for name in ['users','sessions','password_reset_tokens','surat_masuk','surat_keluar','migrations','cache','cache_locks','jobs','job_batches','failed_jobs']:
    mermaid.append(f'    {name} {{')
    for _, col, typ, required, default, pk in conn.execute(f'PRAGMA table_info("{name}")'):
        marker = ' PK' if pk else (' UK' if name == 'users' and col == 'email' or name == 'failed_jobs' and col == 'uuid' else '')
        notes = []
        if not required and not pk:
            notes.append('nullable')
        if name == 'sessions' and col == 'user_id':
            notes.append('referensi logis, bukan FK')
        note = ' "' + '; '.join(notes) + '"' if notes else ''
        mermaid.append(f'        {typ.lower()} {col}{marker}{note}')
    mermaid.append('    }')
(ROOT / 'ERD-relasi-logis.mmd').write_text('\n'.join(mermaid)+'\n',encoding='utf-8')
print(ROOT / 'ERD-relasi-logis.png')

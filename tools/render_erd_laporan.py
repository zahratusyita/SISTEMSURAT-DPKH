from pathlib import Path
from html import escape
import json
from PIL import Image, ImageDraw, ImageFont

root = Path(__file__).resolve().parents[1]
schema = json.loads((root / 'database/schema-mysql.json').read_text())
tables = schema['tables']
relations = schema['relations']
im = Image.new('RGB', (1800, 1480), 'white')
d = ImageDraw.Draw(im)
svg = ['<svg xmlns="http://www.w3.org/2000/svg" width="1800" height="1480" viewBox="0 0 1800 1480">', '<rect width="100%" height="100%" fill="white"/>']
positions = {'users': (670, 40), 'surat_masuk': (80, 610), 'surat_keluar': (1260, 610)}
width, header, row = 460, 48, 40

def line(points, weight=2):
    d.line(points, fill='black', width=weight)
    svg.append('<polyline points="' + ' '.join(f'{x},{y}' for x,y in points) + f'" fill="none" stroke="black" stroke-width="{weight}"/>')

def text(x,y,s,bold=False,size=22):
    font = ImageFont.truetype('C:/Windows/Fonts/' + ('arialbd.ttf' if bold else 'arial.ttf'), size)
    d.text((x,y),s,font=font,fill='black')
    svg.append(f'<text x="{x}" y="{y+size}" font-family="Arial" font-size="{size}" font-weight="{ "bold" if bold else "normal" }">{escape(s)}</text>')

def cy(table,col):
    return positions[table][1]+header+row*next(i for i,c in enumerate(tables[table]) if c['name']==col)+row//2

# Five independently routed foreign-key relationships.
for child,left in [('surat_masuk',True),('surat_keluar',False)]:
    for i,col in enumerate(['created_by','updated_by']):
        sy=cy('users','id')
        ty=cy(child,col)
        edge=670 if left else 1130
        lane=(610-42*i) if left else (1190+42*i)
        end=540 if left else 1260
        line([(edge,sy),(lane,sy),(lane,ty),(end,ty)])
        text(edge-52 if left else edge+8,sy-32,'0..1',size=17)
        text(end+6 if left else end-49,ty-27,'0..N',size=17)
        # Crow's foot at the child table.
        dx=16 if left else -16
        line([(end+dx,ty-9),(end,ty),(end+dx,ty+9)])

sy=cy('surat_masuk','id')
ty=cy('surat_keluar','surat_masuk_id')
line([(80,sy),(30,sy),(30,1390),(1770,1390),(1770,ty),(1720,ty)])
text(32,sy-28,'0..1',size=17)
text(1725,ty-28,'0..N',size=17)
line([(1736,ty-9),(1720,ty),(1736,ty+9)])
text(630,1403,'surat_keluar.surat_masuk_id → surat_masuk.id',size=21)

for name,(x,y) in positions.items():
    h=header+row*len(tables[name])
    line([(x,y),(x+width,y),(x+width,y+h),(x,y+h),(x,y)])
    line([(x,y+header),(x+width,y+header)])
    line([(x+58,y+header),(x+58,y+h)])
    text(x+width//2-len(name)*6,y+11,name,True,24)
    for i,c in enumerate(tables[name]):
        yy=y+header+row*i
        isfk=any(r['child_table']==name and r['child_column']==c['name'] for r in relations)
        marker='PK' if c['column_key']=='PRI' else ('FK' if isfk else '')
        if marker: text(x+10,yy+8,marker,True,19)
        text(x+72,yy+8,c['name'],c['column_key']=='PRI',22)
        if c['column_key']=='PRI': line([(x,yy+row),(x+width,yy+row)])

text(660,490,'PK: Primary Key     FK: Foreign Key',size=20)
text(660,526,'0..1: nol atau satu     0..N: nol atau banyak',size=18)
svg.append('</svg>')
out=root/'database'/'ERD-laporan-PKL'
im.save(str(out)+'.png',dpi=(300,300))
Path(str(out)+'.svg').write_text('\n'.join(svg),encoding='utf-8')
print(out)

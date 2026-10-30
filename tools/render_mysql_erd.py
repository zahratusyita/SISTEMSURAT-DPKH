from pathlib import Path
from html import escape
import json
from PIL import Image, ImageDraw, ImageFont

ROOT = Path(__file__).resolve().parents[1]
schema = json.loads((ROOT/'database/schema-mysql.json').read_text())
tables = schema['tables']
relations = schema['relations']

def render(complete):
    W, H = 1600, 2770 if complete else 1560
    image = Image.new('RGB',(W,H),'white')
    draw = ImageDraw.Draw(image)
    svg = [f'<svg xmlns="http://www.w3.org/2000/svg" width="{W}" height="{H}" viewBox="0 0 {W} {H}">','<rect width="100%" height="100%" fill="white"/>']
    def text(x,y,value,size=19,color='#20334a',bold=False):
        font = ImageFont.truetype('C:/Windows/Fonts/'+('arialbd.ttf' if bold else 'arial.ttf'),size)
        draw.text((x,y),value,font=font,fill=color)
        svg.append(f'<text x="{x}" y="{y+size}" fill="{color}" font-family="Arial,sans-serif" font-size="{size}" font-weight="{"bold" if bold else "normal"}">{escape(value)}</text>')
    def rect(x,y,w,h,fill,stroke=None):
        draw.rectangle((x,y,x+w,y+h),fill=fill,outline=stroke,width=2)
        svg.append(f'<rect x="{x}" y="{y}" width="{w}" height="{h}" fill="{fill}" stroke="{stroke or "none"}" stroke-width="2"/>')
    def line(points,color='#345f91',width=3):
        draw.line(points,fill=color,width=width)
        svg.append(f'<polyline points="{" ".join(f"{x},{y}" for x,y in points)}" fill="none" stroke="{color}" stroke-width="{width}"/>')
    locations = {'users':(595,150),'surat_masuk':(50,630),'surat_keluar':(1140,630)}
    def row_y(name,column):
        return locations[name][1]+48+34*next(i for i,c in enumerate(tables[name]) if c['name']==column)+17
    def table(name,x,y,color='#244e77'):
        columns = tables[name]
        rect(x,y,410,48+34*len(columns),'white','#9eacba')
        rect(x,y,410,48,color)
        text(x+15,y+10,name,23,'white',True)
        for i,c in enumerate(columns):
            yy=y+48+34*i
            if i%2==0: rect(x+2,yy,406,34,'#f3f6f9')
            fk=any(r['child_table']==name and r['child_column']==c['name'] for r in relations)
            marker='PK' if c['column_key']=='PRI' else ('FK' if fk else ('UK' if c['column_key']=='UNI' else ''))
            text(x+12,yy+7,marker,16,'#345f91',True)
            text(x+52,yy+6,c['name'],18,bold=marker=='PK')
            typ=c['type'].replace(' unsigned','')
            typ='str' if typ.startswith('varchar') else ('bigint' if typ.startswith('bigint') else typ)
            if c['nullable']=='YES':typ+='?'
            text(x+307,yy+7,typ,15,'#637487')
    text(50,22,'ERD SIPAS - Database MySQL',34,bold=True)
    text(50,75,f"Skema aktual MySQL {schema['version']} | 11 tabel | 6 foreign key | PK: primary key  FK: foreign key  UK: unique key  ?: nullable",19,'#637487')
    user_y=row_y('users','id')
    for child,side in [('surat_masuk','left'),('surat_keluar','right')]:
        for field,channel in [('created_by',0),('updated_by',1)]:
            target_y=row_y(child,field)
            if side=='left':
                x=535-35*channel
                line([(595,user_y),(x,user_y),(x,target_y),(460,target_y)])
            else:
                x=1065+35*channel
                line([(1005,user_y),(x,user_y),(x,target_y),(1140,target_y)])
    for child in ['surat_masuk','surat_keluar']:
        for field in ['created_by','updated_by']:
            text(465 if child=='surat_masuk' else 1105,row_y(child,field)-24,'0..N',14,'#345f91',True)
    text(541,user_y-27,'0..1',16,'#345f91',True)
    text(1009,user_y-27,'0..1',16,'#345f91',True)
    source_y=row_y('surat_masuk','id')
    target_y=row_y('surat_keluar','surat_masuk_id')
    line([(50,source_y),(20,source_y),(20,1430),(1580,1430),(1580,target_y),(1550,target_y)],'#986b2d')
    text(20,source_y-28,'0..1',17,'#986b2d',True)
    text(1552,target_y-27,'0..N',17,'#986b2d',True)
    text(545,1444,'surat_masuk_id: surat keluar balasan (opsional)',20,'#986b2d',True)
    for name,(x,y) in locations.items():table(name,x,y)
    rect(595,585,410,350,'#f4f7fa','#dbe3eb')
    text(615,605,'Aturan hubungan data',23,bold=True)
    notes=[
        'created_by: administrator pencatat.',
        'updated_by: pengubah terakhir.',
        'Kedua kolom merujuk users.id.',
        'Data lama boleh tanpa pencatat.',
        'Surat keluar boleh tanpa balasan.',
        'Akun memakai soft delete.',
        'Penghapusan fisik pengguna / surat',
        'asal yang dirujuk ditolak (RESTRICT).',
    ]
    for i,note in enumerate(notes):text(615,650+i*32,note,18)
    text(50,1500,'Garis menunjukkan foreign key yang sudah diterapkan. Kardinalitas 0..1 mencakup kolom nullable; pengguna baru wajib dicatat server.',18,'#637487')
    if complete:
        text(50,1575,'Tabel pendukung framework',26,bold=True)
        positions={'sessions':(50,1640),'password_reset_tokens':(595,1640),'migrations':(1140,1640),'cache':(50,1960),'cache_locks':(595,1960),'jobs':(1140,1960),'failed_jobs':(50,2170),'job_batches':(595,2170)}
        # Session relation is depicted separately to keep the full diagram readable.
        text(50,1610,'sessions.user_id -> users.id: satu pengguna dapat memiliki banyak sesi; ON DELETE CASCADE.',18,'#345f91')
        session_y=1640+48+34+17
        line([(595,user_y),(7,user_y),(7,session_y),(50,session_y)],'#447567',2)
        text(12,session_y-25,'0..N',14,'#447567',True)
        for name,(x,y) in positions.items():table(name,x,y,'#586777')
        text(1140,2320,'Tidak ada tabel dashboard.',18)
        text(1140,2350,'Lampiran tercatat pada tabel surat.',18)
        text(1140,2380,'Token reset belum menjadi fitur aktif.',18)
        text(1140,2410,'cache / queue tidak memiliki FK.',18)
        text(50,2720,'Sumber: information_schema dan SHOW CREATE TABLE pada database sipas_db. Diagram inti untuk laporan tersedia sebagai berkas terpisah.',18,'#637487')
    svg.append('</svg>')
    name='ERD-MySQL-lengkap' if complete else 'ERD-MySQL-utama'
    (ROOT/'database'/f'{name}.svg').write_text('\n'.join(svg),encoding='utf-8')
    image.save(ROOT/'database'/f'{name}.png')

render(False)
render(True)
print('ERD utama dan lengkap tersedia dalam PNG/SVG.')

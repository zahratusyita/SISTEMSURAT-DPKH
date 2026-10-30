const { chromium } = require('C:/Users/zahra/.cache/codex-runtimes/codex-primary-runtime/dependencies/node/node_modules/playwright');
const fs = require('fs');
const path = require('path');
const assert = require('assert').strict;
const { execFileSync } = require('node:child_process');

(async () => {
  const base = 'http://127.0.0.1:8001';
  const browser = await chromium.launch({ executablePath: 'C:/Program Files/Google/Chrome/Application/chrome.exe', headless: true });
  const context = await browser.newContext({ viewport: { width: 1440, height: 1000 } });
  const page = await context.newPage();
  const errors = [];
  const failedLocalRequests = [];
  page.on('pageerror', e => errors.push(e.message));
  page.on('response', r => { if (r.url().startsWith(base) && r.status() >= 400) failedLocalRequests.push([r.status(), r.url()]); });
  const results = [];
  const record = results.push.bind(results);
  results.push = value => { console.log('PASS:', value); return record(value); };
  const visit = async url => { const response = await page.goto(base + url); assert(response.status() < 400, `${url}: ${response.status()}`); };
  const fill = async values => { for (const [name,value] of Object.entries(values)) await page.locator(`[name="${name}"]`).fill(value); };
  const submit = async () => {
    const kind = page.url().includes('/surat-masuk/') ? 'masuk' : 'keluar';
    await Promise.all([page.waitForURL(/surat-(masuk|keluar)$/), page.locator(`form[action$="/surat-${kind}"] button[type="submit"]:visible`).first().click()]);
  };
  try {
    fs.mkdirSync('.local', {recursive:true});
    const sample = await page.evaluate(() => {
      const canvas = document.createElement('canvas');
      canvas.width = 1400;
      canvas.height = 500;
      const drawing = canvas.getContext('2d');
      drawing.fillStyle = 'white';
      drawing.fillRect(0, 0, canvas.width, canvas.height);
      drawing.fillStyle = 'black';
      drawing.font = 'bold 64px Arial';
      drawing.fillText('SURAT KELUAR SIPAS', 70, 110);
      drawing.font = '48px Arial';
      drawing.fillText('Nomor: UI/OUT/001', 70, 210);
      drawing.fillText('Tanggal: 16 September 2026', 70, 310);
      drawing.fillText('Perihal: Pengujian surat balasan', 70, 410);
      return canvas.toDataURL('image/png').split(',')[1];
    });
    fs.writeFileSync('.local/ocr-sample.png', Buffer.from(sample, 'base64'));
    await visit('/login');
    await fill({email:'ui-test@example.test',password:'Ui-test123!'});
    await Promise.all([page.waitForURL('**/dashboard'),page.locator('button[type="submit"]').click()]);
    results.push('Login melalui browser dan sesi database');
    for (const url of ['/dashboard','/surat-masuk','/surat-keluar','/profile','/surat-masuk/import','/surat-keluar/import']) await visit(url);
    results.push('Semua menu utama dan halaman impor');
    execFileSync(process.env.SIPAS_PHP || 'C:/xampp/php/php.exe', [path.resolve('tools/create_multi_sheet_fixture.php')]);
    for (const [kind, sheet] of [['masuk', 'Sheet1'], ['keluar', 'Sheet2']]) {
      await visit(`/surat-${kind}/import`);
      await page.locator('input[name="file"]').setInputFiles(path.resolve('.local/agenda-dua-sheet.xlsx'));
      await Promise.all([
        page.waitForResponse(response => response.request().method() === 'POST' && response.url().endsWith(`/surat-${kind}/import`)),
        page.getByRole('button', {name:'Preview Data', exact:true}).click(),
      ]);
      await page.getByRole('heading', {name:'Hasil Baca File'}).waitFor();
      assert((await page.locator('p:has-text("Sheet yang dibaca:")').innerText()).includes(sheet));
      if (kind === 'keluar') {
        await page.waitForFunction(() => document.getAnimations().every(animation => animation.playState === 'finished' || animation.effect?.getTiming().iterations === Infinity));
        await page.screenshot({path:'.local/browser-import-sheet2.png', fullPage:true});
      }
      await Promise.all([
        page.waitForURL(`**/surat-${kind}`),
        page.getByRole('button', {name:'Simpan 1 Data Valid', exact:true}).first().click(),
      ]);
      assert((await page.locator('body').innerText()).includes(`Pengujian impor ${sheet}`));
    }
    results.push('Impor dan konfirmasi satu workbook: Sheet1 masuk, Sheet2 keluar');
    await visit('/surat-masuk/create');
    await fill({tanggal_masuk_surat:'2026-09-16',tanggal_surat:'2026-09-16',nomor_urut:'UI-IN-001',alamat_pengirim:'Instansi Pengujian',nomor_surat:'UI/IN/001',perihal:'Permohonan pengujian browser',tujuan_disposisi:'Bagian Umum'});
    await page.locator('[name="lampiran"]').setInputFiles(path.resolve('.local/ocr-sample.png'));
    await submit();
    const incoming = await page.evaluate(async () => (await (await fetch('/surat-masuk/pilihan-balasan?q=UI%2FIN%2F001')).json())[0]);
    assert(incoming && incoming.id, 'Surat masuk dibuat');
    await visit(`/surat-masuk/${incoming.id}`);
    assert((await page.locator('body').innerText()).includes('Administrator Pengujian'));
    const attachmentHref = await page.locator('a:has-text("Lihat Dokumen")').getAttribute('href');
    const attachmentResponse = await context.request.get(attachmentHref);
    assert.equal(attachmentResponse.status(), 200, 'Lampiran dapat dibuka');
    results.push('Tambah surat masuk, upload lampiran, dan identitas pencatat');
    await visit('/surat-keluar/create');
    await fill({tanggal_keluar_surat:'2026-09-16',tanggal_surat:'2026-09-16',nomor_urut:'UI-OUT-001',alamat_penerima:'Instansi Pengujian',nomor_surat:'UI/OUT/001',perihal:'Balasan pengujian browser',asal:'Bagian Umum'});
    await page.locator('#reply-search').fill('UI/IN/001');
    await page.locator(`#surat_masuk_id option[value="${incoming.id}"]`).waitFor({state:'attached'});
    await page.locator('#surat_masuk_id').selectOption(String(incoming.id));
    await submit();
    await visit('/surat-keluar?search=UI%2FOUT%2F001');
    const outgoingHref = await page.locator('a[title="Detail"]:visible').first().getAttribute('href');
    assert(outgoingHref, 'Tautan detail surat keluar');
    await visit(new URL(outgoingHref).pathname);
    assert(await page.locator(`a[href="${base}/surat-masuk/${incoming.id}"]`).count());
    await page.screenshot({path:'.local/browser-detail-desktop.png',fullPage:true});
    results.push('Pencarian dan pemilihan surat balasan serta tautan detail');
    await page.setViewportSize({width:390,height:844});
    await visit(`/surat-masuk/${incoming.id}`);
    assert(await page.locator(`a[href="${outgoingHref}"]`).count());
    await page.waitForFunction(() => document.querySelector('.sipas-sidebar').getBoundingClientRect().right <= 1);
    await page.locator('[aria-label="Buka menu"]').click();
    await page.waitForFunction(() => document.querySelector('.sipas-sidebar').getBoundingClientRect().left >= -1);
    await page.locator('[aria-label="Tutup menu"]').click();
    await page.waitForFunction(() => document.querySelector('.sipas-sidebar').getBoundingClientRect().right <= 1);
    await page.screenshot({path:'.local/browser-detail-mobile.png',fullPage:true});
    assert(await page.evaluate(() => document.documentElement.scrollWidth <= innerWidth + 2), 'Tidak ada overflow horizontal di detail mobile');
    results.push('Detail balasan pada tampilan mobile');
    await page.setViewportSize({width:1440,height:1000});
    await visit('/surat-keluar');
    const downloadPromise = page.waitForEvent('download');
    await page.locator('#suratKeluarExportLink').click();
    const download = await downloadPromise;
    await download.saveAs('.local/browser-export.xlsx');
    assert(fs.statSync('.local/browser-export.xlsx').size > 1000);
    results.push('Unduhan ekspor Excel melalui browser');
    await visit('/surat-keluar/create');
    await page.waitForFunction(() => !!window.SipasOcr);
    const text = await page.evaluate(async () => {
      const blob = await (await fetch('/assets/__ocr_test.png')).blob();
      return window.SipasOcr.recognize(blob);
    });
    assert(/SURAT|Nomor|SIPAS/i.test(text), 'OCR membaca gambar');
    results.push('OCR Tesseract membaca gambar dengan aset lokal');
    assert.deepEqual(errors, [], 'Tidak ada error JavaScript');
    assert.deepEqual(failedLocalRequests, [], 'Tidak ada respons HTTP error dari aplikasi/aset lokal');
    fs.writeFileSync('.local/browser-results.json',JSON.stringify({passed:results,errors,failedLocalRequests},null,2));
    console.log(JSON.stringify({passed:results,errors,failedLocalRequests},null,2));
  } finally { await browser.close(); }
})().catch(error => { console.error(error); process.exitCode=1; });

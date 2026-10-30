// Convert inspected MySQL DDL using dbdiagram's official parser and validate DBML.
const fs = require('fs');
const path = require('path');
const assert = require('node:assert/strict');
process.on('uncaughtException', error => {
  console.error(error.diags?.map(diagnostic => diagnostic.message).join('\n') || error.message);
  process.exitCode = 1;
});
const root = path.resolve(__dirname, '..');
const { importer, Parser } = require(process.env.DBML_CORE_PATH || path.join(root, '.local/dbml-validator/node_modules/@dbml/core'));
const actual = JSON.parse(fs.readFileSync(path.join(root, 'database/schema-mysql.json'), 'utf8'));
const ddl = fs.readFileSync(path.join(root, 'database/schema-mysql.sql'), 'utf8');
const statements = [...ddl.matchAll(/CREATE TABLE `([^`]+)`[\s\S]*?;/g)];
assert.equal(statements.length, 11);
assert.equal(new Set(statements.map(item => item[1])).size, 11);
const primary = new Set(['users', 'surat_masuk', 'surat_keluar']);
const outputs = [
  {name:'SIPAS-dbdiagram-lengkap', statements, keys:6},
  {name:'SIPAS-dbdiagram-utama', statements:statements.filter(item => primary.has(item[1])), keys:5},
];
for (const output of outputs) {
  const sql = output.statements.map(item => item[0]).join('\n\n') + '\n';
  // Keep classic relationship operators for compatibility; nullable fields convey optionality.
  let converted = importer.import(sql, 'mysql', {includeRecords:false}).replace(/\?([<>])\?/g, '$1');
  // The SQL converter omits MySQL UNSIGNED; preserve the inspected column types explicitly.
  converted = converted.replace(/Table "([^"]+)" \{([\s\S]*?)\n\}/g, (block, name, body) => {
    const columns = new Map(actual.tables[name].map(column => [column.name, column]));
    const fields = body.replace(/^  "([^"]+)" (\S+)/gm, (line, field, type) => {
      const column = columns.get(field);
      return column?.type.endsWith(' unsigned') ? `  "${field}" ${JSON.stringify(column.type)}` : line;
    });
    return `Table "${name}" {${fields}\n}`;
  });
  const dbml = '// Ganti SELURUH isi editor dbdiagram dengan file ini; jangan tambahkan ke definisi lama.\n'
    + '// Sumber: skema MySQL SIPAS aktual. Tidak mengandung data akun atau sesi.\n'
    + "Project SIPAS {\n  database_type: 'MySQL'\n  Note: 'Sistem Informasi Pengelolaan Administrasi Surat; relasi sesuai skema MySQL aktual.'\n}\n\n"
    + converted + '\n';
  for (const format of ['dbml', 'dbmlv2']) {
    const model = new Parser().parse(dbml, format);
    const tables = model.schemas.flatMap(schema => schema.tables);
    const refs = model.schemas.flatMap(schema => schema.refs);
    assert.equal(tables.length, output.statements.length);
    assert.equal(new Set(tables.map(table => table.name)).size, tables.length);
    assert.equal(refs.length, output.keys);
    for (const table of tables) {
      assert.deepEqual(table.fields.map(field => field.name), actual.tables[table.name].map(column => column.name));
      for (const field of table.fields) {
        const column = actual.tables[table.name].find(column => column.name === field.name);
        assert.equal(field.type.type_name.toLowerCase(), column.type.toLowerCase());
        assert.equal(Boolean(field.not_null), column.nullable === 'NO');
      }
    }
  }
  fs.writeFileSync(path.join(root, 'database', output.name + '.dbml'), dbml);
  // Plain CREATE TABLE only, for the Import > MySQL option on a fresh diagram.
  fs.writeFileSync(path.join(root, 'database', output.name + '.sql'), sql);
  console.log(`${output.name}: ${output.statements.length} tabel, ${output.keys} relasi, parser DBML lama dan baru lulus.`);
}

<?php

$root = dirname(__DIR__);
$credentials = json_decode(file_get_contents($root.'/.local/mysql-credentials.json'), true, flags: JSON_THROW_ON_ERROR);
$pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=sipas_db;charset=utf8mb4', 'sipas_app', $credentials['app_password'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
$schema = ['version' => $pdo->query('SELECT VERSION()')->fetchColumn(), 'database' => 'sipas_db', 'tables' => [], 'relations' => []];
$ddl = ['-- Skema aktual SIPAS pada MySQL; tanpa data dan tanpa kredensial.', 'SET FOREIGN_KEY_CHECKS=0;'];
$mermaid = ['erDiagram'];
$relations = $pdo->query('SELECT k.TABLE_NAME AS child_table, k.COLUMN_NAME AS child_column, k.REFERENCED_TABLE_NAME AS parent_table, k.REFERENCED_COLUMN_NAME AS parent_column, r.DELETE_RULE AS delete_rule FROM information_schema.KEY_COLUMN_USAGE k JOIN information_schema.REFERENTIAL_CONSTRAINTS r ON r.CONSTRAINT_SCHEMA=k.CONSTRAINT_SCHEMA AND r.CONSTRAINT_NAME=k.CONSTRAINT_NAME AND r.TABLE_NAME=k.TABLE_NAME WHERE k.TABLE_SCHEMA=DATABASE() AND k.REFERENCED_TABLE_NAME IS NOT NULL ORDER BY k.TABLE_NAME,k.COLUMN_NAME')->fetchAll(PDO::FETCH_ASSOC);
$schema['relations'] = $relations;
foreach ($relations as $relation) {
    $mermaid[] = '    '.$relation['parent_table'].' |o..o{ '.$relation['child_table'].' : "'.$relation['child_column'].'"';
}
foreach ($tables as $table) {
    $stmt = $pdo->prepare('SELECT COLUMN_NAME AS name, COLUMN_TYPE AS type, IS_NULLABLE AS nullable, COLUMN_KEY AS column_key FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME=? ORDER BY ORDINAL_POSITION');
    $stmt->execute([$table]);
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $schema['tables'][$table] = $columns;
    $ddl[] = $pdo->query('SHOW CREATE TABLE `'.$table.'`')->fetch(PDO::FETCH_NUM)[1].';';
    $mermaid[] = '    '.$table.' {';
    foreach ($columns as $column) {
        $isForeign = count(array_filter($relations, fn ($r) => $r['child_table'] === $table && $r['child_column'] === $column['name'])) > 0;
        $marker = $column['column_key'] === 'PRI' ? ' PK' : ($isForeign ? ' FK' : ($column['column_key'] === 'UNI' ? ' UK' : ''));
        $type = preg_replace('/[^a-z0-9_]/', '_', $column['type']);
        $mermaid[] = '        '.$type.' '.$column['name'].$marker.($column['nullable'] === 'YES' ? ' "nullable"' : '');
    }
    $mermaid[] = '    }';
}
$ddl[] = 'SET FOREIGN_KEY_CHECKS=1;';
file_put_contents($root.'/database/schema-mysql.json', json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
file_put_contents($root.'/database/schema-mysql.sql', implode("\n\n", $ddl)."\n");
file_put_contents($root.'/database/ERD-MySQL.mmd', implode("\n", $mermaid)."\n");
echo count($tables).' tabel dan '.count($relations).' foreign key diekspor dari skema MySQL aktual.'.PHP_EOL;

<?php
/**
 * Database regression tests using phpBB's real SQLite driver, schema tools and migrator.
 * PHPBB_ROOT_PATH=/path/to/phpBB php tests/ernadoo_migration.php
 * No board database is accessed. Every scenario uses an in-memory database.
 */
namespace Symfony\Component\DependencyInjection {
	// Only service lookup/injection is needed; no Symfony installation is required.
	interface ContainerInterface { public function get($id); }
	interface ContainerAwareInterface { public function setContainer(?ContainerInterface $container = null); }
}

namespace {
	define('IN_PHPBB', true);
	$root = rtrim(getenv('PHPBB_ROOT_PATH') ?: '', '/') . '/';
	if (!is_file($root . 'phpbb/db/migrator.php'))
	{
		fwrite(STDERR, "Set PHPBB_ROOT_PATH to a phpBB 3.3 source directory.\n");
		exit(1);
	}
	spl_autoload_register(function ($class) use ($root) {
		$paths = [
			'phpbb\\' => $root . 'phpbb/',
			'kaileymsnay\\qte\\' => __DIR__ . '/../',
		];
		foreach ($paths as $prefix => $path)
		{
			if (strpos($class, $prefix) === 0)
			{
				$file = $path . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
				if (is_file($file)) { require $file; }
			}
		}
	});

	class MigrationTestContainer implements \Symfony\Component\DependencyInjection\ContainerInterface
	{
		public $services;
		public function __construct($services) { $this->services = $services; }
		public function get($id) { return $this->services[$id]; }
	}
	function check_migration($condition, $message)
	{
		if (!$condition) { throw new \RuntimeException($message); }
	}
	function rows($db, $table)
	{
		$result = $db->sql_query('SELECT * FROM t_' . $table);
		$rows = $db->sql_fetchrowset($result);
		$db->sql_freeresult($result);
		return $rows;
	}
	function fixture($root, $legacy = true)
	{
		$db = new \phpbb\db\driver\sqlite3();
		$db->sql_connect(':memory:', '', '', '');
		$tools = new \phpbb\db\tools\tools($db);
		$config = new \phpbb\config\config($legacy ? ['qte_version' => '2.0.0-RC1'] : []);
		foreach ([
			'ext' => 'ext_name VARCHAR(255), ext_active INTEGER',
			'forums' => 'forum_id INTEGER PRIMARY KEY',
			'topics' => 'topic_id INTEGER PRIMARY KEY',
			'config_text' => 'config_name VARCHAR(255), config_value TEXT',
			'acl_options' => 'auth_option_id INTEGER PRIMARY KEY, auth_option VARCHAR(255), is_local INTEGER, is_global INTEGER',
			'acl_groups' => 'group_id INTEGER, forum_id INTEGER, auth_option_id INTEGER, auth_role_id INTEGER, auth_setting INTEGER',
			'acl_users' => 'user_id INTEGER, forum_id INTEGER, auth_option_id INTEGER, auth_role_id INTEGER, auth_setting INTEGER',
			'acl_roles_data' => 'role_id INTEGER, auth_option_id INTEGER, auth_setting INTEGER',
			'modules' => 'module_id INTEGER PRIMARY KEY, module_basename VARCHAR(255), module_class VARCHAR(10), module_mode VARCHAR(255), module_auth VARCHAR(255), module_enabled INTEGER, parent_id INTEGER, left_id INTEGER, right_id INTEGER',
		] as $name => $columns)
		{
			$db->sql_query('CREATE TABLE t_' . $name . ' (' . $columns . ')');
		}
		$initial = new \kaileymsnay\qte\migrations\v10x\m1_initial_schema($config, $db, $tools, $root, 'php', 't_');
		$schema = $initial->update_schema();
		// RC1 has the same columns, with a 60-character description and implicit VCHAR lengths.
		$schema['add_tables']['t_topics_attr']['COLUMNS']['attr_desc'] = ['VCHAR:60', ''];
		$schema['add_tables']['t_topics_attr']['COLUMNS']['attr_name'] = ['VCHAR', ''];
		$schema['add_tables']['t_topics_attr']['COLUMNS']['attr_img'] = ['VCHAR', ''];
		$tools->perform_schema_changes($schema);
		if ($legacy)
		{
			$db->sql_query("INSERT INTO t_ext VALUES ('ernadoo/qte', 0)");
			$db->sql_query("INSERT INTO t_acl_options VALUES (1, 'a_attr_manage', 0, 1), (2, 'm_qte_attr_edit', 1, 0), (3, 'm_qte_attr_del', 1, 0), (4, 'f_qte_attr_7', 1, 0)");
			$db->sql_query("INSERT INTO t_modules VALUES (20, '\\ernadoo\\qte\\acp\\main_module', 'acp', 'manage', 'ext_ernadoo/qte && acl_a_attr_manage', 1, 5, 12, 13)");
			$db->sql_query("INSERT INTO t_topics_attr (attr_id, attr_type, attr_name, left_id, right_id, attr_colour, attr_desc) VALUES (7, 0, 'QTE_REUP', 1, 2, 'ff0000', 'Original description')");
			$db->sql_query("INSERT INTO t_topics_attr (attr_id, attr_type, attr_name, left_id, right_id, attr_img, attr_date, attr_user_colour) VALUES (11, 1, 'Solución por %mod% :: %date%', 3, 4, 'ext/ernadoo/qte/custom.png', 'd/m/Y', 1)");
			$db->sql_query('INSERT INTO t_topics VALUES (101, 7, 42, 123456), (102, 11, 43, 234567), (103, 0, 0, 0)');
			$db->sql_query('INSERT INTO t_forums VALUES (5, 1, 7)');
			$db->sql_query('INSERT INTO t_acl_groups VALUES (8, 5, 3, 0, -1), (9, 5, 3, 0, 1), (8, 5, 4, 0, 1)');
			$db->sql_query('INSERT INTO t_acl_users VALUES (42, 5, 3, 0, 0), (43, 5, 3, 0, 1)');
			$db->sql_query('INSERT INTO t_acl_roles_data VALUES (6, 3, 1), (7, 3, -1), (6, 4, 1)');
		}
		$cache = new class {
			public $keys = [];
			public function destroy($key) { $this->keys[] = $key; }
		};
		$auth = new class {
			public $cleared = false;
			public function acl_clear_prefetch() { $this->cleared = true; }
		};
		$dispatcher = new class {
			public function disable() {}
			public function enable() {}
		};
		$container = new MigrationTestContainer(['cache' => $cache, 'auth' => $auth, 'dispatcher' => $dispatcher]);
		$migration = new \kaileymsnay\qte\migrations\v10x\m4_ernadoo_compatibility($config, $db, $tools, $root, 'php', 't_');
		$migration->setContainer($container);
		return [$db, $tools, $config, $container, $migration];
	}

	// Exercise the full extension migration chain, not just the custom callback.
	[$db, $tools, $config, $container, $migration] = fixture($root);
	$preserved = [];
	foreach (['topics_attr', 'topics', 'forums', 'acl_groups', 'acl_users', 'acl_roles_data'] as $name) { $preserved[$name] = rows($db, $name); }
	$migrator = new \phpbb\db\migrator($container, $config, $db, $tools, 't_migrations', $root, 'php', 't_', [
		new \phpbb\db\migration\tool\config_text(new \phpbb\config\db_text($db, 't_config_text')),
	], new \phpbb\db\migration\helper());
	$migrator->create_migrations_table();
	$migrator->populate_migrations(['\\phpbb\\db\\migration\\data\\v33x\\v338']);
	$classes = [];
	foreach (['m1_initial_schema', 'm2_initial_data', 'm3_remove_version', 'm4_ernadoo_compatibility'] as $name)
	{
		$classes[] = '\\kaileymsnay\\qte\\migrations\\v10x\\' . $name;
	}
	$migrator->set_migrations($classes);
	for ($i = 0; !$migrator->finished() && $i < 30; ++$i) { $migrator->update(); }
	check_migration($migrator->finished(), 'Full migration chain did not finish');
	foreach ($preserved as $name => $expected) { check_migration(rows($db, $name) === $expected, $name . ' data changed'); }
	$modules = rows($db, 'modules');
	check_migration($modules[0]['module_basename'] === '\\kaileymsnay\\qte\\acp\\main_module' && $modules[0]['module_auth'] === 'ext_kaileymsnay/qte && acl_a_attr_manage', 'ACP module not converted');
	check_migration((int) $modules[0]['module_id'] === 20 && (int) $modules[0]['left_id'] === 12, 'Module position changed');
	check_migration(rows($db, 'acl_options')[2]['auth_option'] === 'm_qte_attr_delete', 'Delete permission not renamed');
	check_migration($container->get('auth')->cleared && in_array('_attr', $container->get('cache')->keys), 'Caches not invalidated');
	$result = $db->sql_query('PRAGMA table_info(t_topics_attr)');
	$columns = $db->sql_fetchrowset($result);
	$db->sql_freeresult($result);
	$description = array_values(array_filter($columns, function ($column) { return $column['name'] === 'attr_desc'; }));
	check_migration(stripos($description[0]['type'], '255') !== false, 'Description was not widened');
	$migration->migrate_ernadoo();
	check_migration(rows($db, 'modules') === $modules, 'Repeat run changed module data');
	echo "PASS: full migration chain, data/ACL preservation, description width, module identity, cache invalidation, repeat run\n";

	// Both permission names already exist: preserve unrelated ACLs and merge conservatively.
	[$db, $tools, $config, $container, $migration] = fixture($root);
	$db->sql_query("INSERT INTO t_acl_options VALUES (10, 'm_qte_attr_delete', 1, 0)");
	$db->sql_query('INSERT INTO t_acl_groups VALUES (8, 5, 10, 0, 1), (77, 5, 10, 0, 1)');
	$db->sql_query('INSERT INTO t_acl_users VALUES (42, 5, 10, 0, 1)');
	$db->sql_query('INSERT INTO t_acl_roles_data VALUES (7, 10, 1)');
	$db->sql_query("INSERT INTO t_modules VALUES (21, '\\kaileymsnay\\qte\\acp\\main_module', 'acp', 'manage', 'ext_kaileymsnay/qte && acl_a_attr_manage', 1, 5, 14, 15)");
	$migration->migrate_ernadoo();
	foreach (['acl_groups' => [8 => -1, 9 => 1, 77 => 1], 'acl_users' => [42 => 0, 43 => 1], 'acl_roles_data' => [6 => 1, 7 => -1]] as $name => $expected)
	{
		$key = ['acl_groups' => 'group_id', 'acl_users' => 'user_id', 'acl_roles_data' => 'role_id'][$name];
		$actual = [];
		foreach (rows($db, $name) as $row) { if ((int) $row['auth_option_id'] === 10) { $actual[(int) $row[$key]] = (int) $row['auth_setting']; } }
		ksort($actual); ksort($expected);
		check_migration($actual === $expected, 'Coexisting permission merge failed: ' . $name);
	}
	check_migration((int) rows($db, 'modules')[0]['module_enabled'] === 0 && (int) rows($db, 'modules')[1]['module_enabled'] === 1, 'Duplicate legacy module not disabled');
	$before = rows($db, 'acl_groups');
	$migration->migrate_ernadoo();
	check_migration(rows($db, 'acl_groups') === $before, 'Repeat merge duplicated ACL rows');
	echo "PASS: coexisting old/new ACLs, conflict handling, duplicate module, repeat merge\n";

	foreach (['active', 'old_version', 'partial_schema', 'missing_column', 'missing_permission', 'missing_module'] as $case)
	{
		[$db, $tools, $config, $container, $migration] = fixture($root);
		if ($case === 'active') { $db->sql_query('UPDATE t_ext SET ext_active = 1'); }
		if ($case === 'old_version') { $config->set('qte_version', '1.2.5'); }
		if ($case === 'partial_schema') { $db->sql_query("ALTER TABLE t_topics_attr ADD attr_auths TEXT DEFAULT ''"); }
		if ($case === 'missing_column') { $tools->sql_column_remove('t_topics_attr', 'attr_date'); }
		if ($case === 'missing_permission') { $db->sql_query('DELETE FROM t_acl_options WHERE auth_option_id = 2'); }
		if ($case === 'missing_module') { $db->sql_query('DELETE FROM t_modules'); }
		$before = rows($db, 'acl_options');
		try { $migration->migrate_ernadoo(); throw new \RuntimeException('Expected refusal: ' . $case); }
		catch (\phpbb\db\migration\exception $e) { check_migration(rows($db, 'acl_options') === $before, 'Refusal changed ACLs: ' . $case); }
	}
	[$db, $tools, $config, $container, $migration] = fixture($root, false);
	$migration->migrate_ernadoo();
	check_migration(rows($db, 'acl_options') === [] && $container->get('cache')->keys === [], 'Non-legacy installation changed');
	echo "PASS: active/unsupported/incomplete source refusal, non-legacy no-op\n";
}

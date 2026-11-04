<?php
/** Standalone listener regression checks: php tests/recenttopicsng.php */
namespace Symfony\Component\EventDispatcher {
	interface EventSubscriberInterface
	{
		public static function getSubscribedEvents();
	}
}

namespace {
	require __DIR__ . '/../event/main_listener.php';

	class IntegrationEvent extends \ArrayObject
	{
		public function update_subarray($key, $name, $value)
		{
			$data = $this[$key];
			$data[$name] = $value;
			$this[$key] = $data;
		}
	}

	function check($condition, $message)
	{
		if (!$condition)
		{
			throw new \RuntimeException($message);
		}
	}

	$qte = new class {
		public $topics = [];
		public $calls = [];
		public function get_users_by_topic_id($topics)
		{
			$this->topics = $topics;
		}
		public function attr_display($id, $user, $time)
		{
			$this->calls[] = [$id, $user, $time];
			return '<span class="qte_solved">Solved</span>';
		}
	};
	$class = new \ReflectionClass(\kaileymsnay\qte\event\main_listener::class);
	$listener = $class->newInstanceWithoutConstructor();
	$property = $class->getProperty('qte');
	$property->setAccessible(true);
	$property->setValue($listener, $qte);
	$events = $listener::getSubscribedEvents();
	foreach (['modify_topics_list', 'modify_tpl_ary'] as $name)
	{
		check(isset($events['imcger.recenttopicsng.' . $name]), 'Missing subscription: ' . $name);
	}

	$listener->recenttopicsng_modify_topics_list(new IntegrationEvent(['topic_list' => [17, 23]]));
	check($qte->topics === [17, 23], 'Attribute authors must be loaded for the complete topic list');

	$event = new IntegrationEvent([
		'row' => ['topic_attr_id' => 3, 'topic_attr_user' => 12, 'topic_attr_time' => 123456],
		'tpl_ary' => ['TOPIC_TITLE' => 'Example', 'U_VIEW_TOPIC' => '/viewtopic.php?t=17'],
	]);
	$listener->recenttopicsng_modify_tpl_ary($event);
	check($qte->calls === [[3, 12, 123456]], 'Render through QTE with the original attribute metadata');
	check($event['tpl_ary']['TOPIC_ATTRIBUTE'] === '<span class="qte_solved">Solved</span>', 'Preserve attribute HTML');
	check($event['tpl_ary']['TOPIC_TITLE'] === 'Example' && $event['tpl_ary']['U_VIEW_TOPIC'] === '/viewtopic.php?t=17', 'Preserve title and link');

	foreach ([['topic_attr_id' => 0], []] as $row)
	{
		$event['row'] = $row;
		$listener->recenttopicsng_modify_tpl_ary($event);
		check($event['tpl_ary']['TOPIC_ATTRIBUTE'] === false, 'Unmarked topics must not inherit an attribute');
	}
	check(count($qte->calls) === 1, 'Do not render an attribute for unmarked topics');
	$listener->recenttopicsng_modify_topics_list(new IntegrationEvent(['topic_list' => []]));
	check($qte->topics === [], 'Handle an empty recent topics list');
	echo "PASS: subscriptions, author loading, attribute rendering, title/link preservation, unmarked topics, empty list\n";
}

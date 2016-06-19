<?php
/*
 *  本页面的配置，可用于数据请求、面包屑导航、侧边栏等多个场景
 *	本页面可被重复include，
 *	如：侧边栏时引入本页，则会在读取到配置后return，不会发生请求和输出页面代码
 *	如：正文中引入本页，则会发生请求并执行页面输出。
 */
	if (!Utility::breadCrumb(array(
					 'parent'   => '其他管理'
					,'name'     => '缓存管理'
					,'rank'     => 95
					,'isAuthed' =>  is_object($currentUserResult) && $currentUserResult->find('level')>=5
					,'url'      => Utility::getCurrentFileUrl()
					,'params'   => array()
					,'urlParam'   => 'cache/info'
					))){return;}
/*
 *	变量：请求参数，用于数据请求和翻页设定
 *	支持默认参数、$_REQUEST、锁定参数，三者合并（后者覆盖前者）
 */
	$params = Utility::getParamsOfListInRequest(array('size'=>DEFAULT_PAGE_SIZE),$breadCrumb['params']);

/*
 * 发起请求，并得到请求结果。（params参数中的分页信息也会被更新）
 * 如果不指定报错处理，则直接打印错误信息并退出。
 */
	$requestResult = Utility::requestBreadCrumb($params);

	if (!function_exists('getRedisInfoTitleOfKey'))
	{
		function getRedisInfoTitleOfKey($key)
		{
			$keyList = array(
						'multiplexing_api'            =>'redis的事件循环机制'
						,'run_id'                      =>'标识redis server的随机值'
						,'uptime_in_seconds'           =>'redis server启动的时间(单位s)'
						,'uptime_in_days'              =>'redis server启动的时间(单位d)'
						,'lru_clock'                   =>'Clock incrementing every minute, for LRU management TODO 不清楚是如何计算的'
						,'connected_clients'           =>'连接的客户端数'
						,'client_longest_output_list'  =>'当前客户端连接的最大输出列表	TODO'
						,'client_biggest_input_buf'    =>'当前客户端连接的最大输入buffer TODO'
						,'blocked_clients'             =>'被阻塞的客户端数'
						,'used_memory'                 =>'使用内存，单位B'
						,'used_memory_human'           =>'human read显示使用内存'
						,'used_memory_rss'             =>'系统给redis分配的内存（即常驻内存）'
						,'used_memory_peak'            =>'内存使用的峰值大小'
						,'used_memory_peak_human'      =>'human read显示内存使用峰值'
						,'used_memory_lua'             =>'lua引擎使用的内存'
						,'mem_fragmentation_ratio'     =>'used_memory_rss/used_memory比例，一般情况下，used_memory_rss略高于used_memory，当内存碎片较多时，则mem_fragmentation_ratio会较大，可以反映内存碎片是否很多'
						,'mem_allocator'               =>'内存分配器'
						,'rdb_changes_since_last_save' =>'自上次dump后rdb的改动'
						,'rdb_bgsave_in_progress'      =>'标识rdb save是否进行中'
						,'rdb_last_save_time'          =>'上次save的时间戳'
						,'rdb_last_bgsave_status'      =>'上次的save操作状态'
						,'rdb_last_bgsave_time_sec'    =>'上次rdb save操作使用的时间(单位s)'
						,'rdb_current_bgsave_time_sec' =>'如果rdb save操作正在进行，则是所使用的时间'
						,'aof_enabled'                 =>'是否开启aof，默认没开启'
						,'aof_rewrite_in_progress'     =>'标识aof的rewrite操作是否在进行中'
						,'aof_rewrite_scheduled'       =>'标识是否将要在rdb save操作结束后执行'
						,'aof_last_rewrite_time_sec'   =>'上次rewrite操作使用的时间(单位s)'
						,'aof_last_bgrewrite_status'   =>'上次rewrite操作的状态'
						,'aof_current_size'            =>'aof当前大小'
						,'aof_base_size'               =>'aof上次启动或rewrite的大小'
						,'aof_pending_rewrite'         =>'同上面的aof_rewrite_scheduled'
						,'aof_buffer_length'           =>'aof buffer的大小'
						,'aof_rewrite_buffer_length'   =>'aof rewrite buffer的大小'
						,'aof_pending_bio_fsync'       =>'后台IO队列中等待fsync任务的个数'
						,'aof_delayed_fsync'           =>'延迟的fsync计数器 TODO'
						,'total_connections_received'  =>'自启动起连接过的总数'
						,'total_commands_processed'    =>'自启动起运行命令的总数'
						,'instantaneous_ops_per_sec'   =>'每秒执行的命令个数'
						,'rejected_connections'        =>'因为最大客户端连接书限制，而导致被拒绝连接的个数'
						,'expired_keys'                =>'自启动起过期的key的总数'
						,'evicted_keys'                =>'因为内存大小限制，而被驱逐出去的键的个数'
						,'keyspace_hits'               =>'在main dictionary(todo)中成功查到的key个数'
						,'keyspace_misses'             =>'同上，未查到的key的个数'
						,'pubsub_channels'             =>'发布/订阅频道数'
						,'pubsub_patterns'             =>'发布/订阅模式数'
						,'latest_fork_usec'            =>'上次的fork操作使用的时间（单位ms）'
						,'role'                        =>'角色'
						,'connected_slaves'            =>'连接的从库数'
						,'master_sync_in_progress'     =>'标识主redis正在同步到从redis'
						,'used_cpu_sys'                =>'redis server的sys cpu使用率'
						,'used_cpu_user'               =>'redis server的user cpu使用率'
						,'used_cpu_sys_children'       =>'后台进程的sys cpu使用率'
						,'used_cpu_user_children'      =>'后台进程的user cpu使用率'
							);

			return isset($keyList[$key])?$keyList[$key]:'';
		}

		function getRedisInfoDesOfKey($key)
		{
			$keyList = array(
						'redis_version'                   => 'Version of the Redis server'
						,'redis_git_sha1'                  => 'Git SHA1'
						,'redis_git_dirty'                 => 'Git dirty flag'
						,'os'                              => 'Operating system hosting the Redis server'
						,'arch_bits'                       => 'Architecture (32 or 64 bits)'
						,'multiplexing_api'                => 'event loop mechanism used by Redis'
						,'gcc_version'                     => 'Version of the GCC compiler used to compile the Redis server'
						,'process_id'                      => 'PID of the server process'
						,'run_id'                          => 'Random value identifying the Redis server (to be used by Sentinel and Cluster)'
						,'tcp_port'                        => 'TCP/IP listen port'
						,'uptime_in_seconds'               => 'Number of seconds since Redis server start'
						,'uptime_in_days'                  => 'Same value expressed in days'
						,'lru_clock'                       => 'Clock incrementing every minute, for LRU management'
						,'connected_clients'               => 'Number of client connections (excluding connections from slaves)'
						,'client_longest_output_list'      => 'longest output list among current client connections'
						,'client_biggest_input_buf'        => 'biggest input buffer among current client connections'
						,'blocked_clients'                 => 'Number of clients pending on a blocking call (BLPOP, BRPOP, BRPOPLPUSH)'
						,'used_memory'                     => 'total number of bytes allocated by Redis using its allocator (either standard libc, jemalloc, or an alternative allocator such as tcmalloc'
						,'used_memory_human'               => 'Human readable representation of previous value'
						,'used_memory_rss'                 => 'Number of bytes that Redis allocated as seen by the operating system (a.k.a resident set size). This is the number reported by tools such as top(1) and ps(1)'
						,'used_memory_peak'                => 'Peak memory consumed by Redis (in bytes)'
						,'used_memory_peak_human'          => 'Human readable representation of previous value'
						,'used_memory_lua'                 => 'Number of bytes used by the Lua engine'
						,'mem_fragmentation_ratio'         => 'Ratio between used_memory_rss and used_memory'
						,'mem_allocator'                   => 'Memory allocator, chosen at compile time'
						,'loading'                         => 'Flag indicating if the load of a dump file is on-going'
						,'rdb_changes_since_last_save'     => 'Number of changes since the last dump'
						,'rdb_bgsave_in_progress'          => 'Flag indicating a RDB save is on-going'
						,'rdb_last_save_time'              => 'Epoch-based timestamp of last successful RDB save'
						,'rdb_last_bgsave_status'          => 'Status of the last RDB save operation'
						,'rdb_last_bgsave_time_sec'        => 'Duration of the last RDB save operation in seconds'
						,'rdb_current_bgsave_time_sec'     => 'Duration of the on-going RDB save operation if any'
						,'aof_enabled'                     => 'Flag indicating AOF logging is activated'
						,'aof_rewrite_in_progress'         => 'Flag indicating a AOF rewrite operation is on-going'
						,'aof_rewrite_scheduled'           => 'Flag indicating an AOF rewrite operation will be scheduled once the on-going RDB save is complete.'
						,'aof_last_rewrite_time_sec'       => 'Duration of the last AOF rewrite operation in seconds'
						,'aof_current_rewrite_time_sec'    => 'Duration of the on-going AOF rewrite operation if any'
						,'aof_last_bgrewrite_status'       => 'Status of the last AOF rewrite operation'
						,'aof_current_size'                => 'AOF current file size'
						,'aof_base_size'                   => 'AOF file size on latest startup or rewrite'
						,'aof_pending_rewrite'             => 'Flag indicating an AOF rewrite operation will be scheduled once the on-going RDB save is complete.'
						,'aof_buffer_length'               => 'Size of the AOF buffer'
						,'aof_rewrite_buffer_length'       => 'Size of the AOF rewrite buffer'
						,'aof_pending_bio_fsync'           => 'Number of fsync pending jobs in background I/O queue'
						,'aof_delayed_fsync'               => 'Delayed fsync counter'
						,'loading_start_time'              => 'Epoch-based timestamp of the start of the load operation'
						,'loading_total_bytes'             => 'Total file size'
						,'loading_loaded_bytes'            => 'Number of bytes already loaded'
						,'loading_loaded_perc'             => 'Same value expressed as a percentage'
						,'loading_eta_seconds'             => 'ETA in seconds for the load to be complete'
						,'total_connections_received'      => 'Total number of connections accepted by the server'
						,'total_commands_processed'        => 'Total number of commands processed by the server'
						,'instantaneous_ops_per_sec'       => 'Number of commands processed per second'
						,'rejected_connections'            => 'Number of connections rejected because of maxclients limit'
						,'expired_keys'                    => 'Total number of key expiration events'
						,'evicted_keys'                    => 'Number of evicted keys due to maxmemory limit'
						,'keyspace_hits'                   => 'Number of successful lookup of keys in the main dictionary'
						,'keyspace_misses'                 => 'Number of failed lookup of keys in the main dictionary'
						,'pubsub_channels'                 => 'Global number of pub/sub channels with client subscriptions'
						,'pubsub_patterns'                 => 'Global number of pub/sub pattern with client subscriptions'
						,'latest_fork_usec'                => 'Duration of the latest fork operation in microseconds'
						,'role'                            => 'Value is "master" if the instance is slave of no one, or "slave" if the instance is enslaved to a master. Note that a slave can be master of another slave (daisy chaining).'
						,'master_host'                     => 'Host or IP address of the master'
						,'master_port'                     => 'Master listening TCP port'
						,'master_link_status'              => 'Status of the link (up/down)'
						,'master_last_io_seconds_ago'      => 'Number of seconds since the last interaction with master'
						,'master_sync_in_progress'         => 'Indicate the master is syncing to the slave'
						,'master_sync_left_bytes'          => 'Number of bytes left before syncing is complete'
						,'master_sync_last_io_seconds_ago' => 'Number of seconds since last transfer I/O during a SYNC operation'
						,'master_link_down_since_seconds'  => 'Number of seconds since the link is down'
						,'connected_slaves'                => 'Number of connected slaves'
						,'slaveXXX'                        => 'id, IP address, port, state'
						,'used_cpu_sys'                    => 'System CPU consumed by the Redis server'
						,'used_cpu_sys_children'           => 'System CPU consumed by the background processes'
						,'used_cpu_user_children'          => 'User CPU consumed by the background processes'
						,'cmdstat_XXX'                     => 'calls=XXX,usec=XXX,usec_per_call=XXX'
						,'cluster_enabled'                 => 'Indicate Redis cluster is enabled'
						,'dbXXX'                           => 'keys=XXX,expires=XXX'
							);

			return isset($keyList[$key])?$keyList[$key]:'';
		}
	}

?>

<?= Utility::strOfBreadCrumb($breadCrumb) ?>

<div class="search_nav form-inline clearfix">
	<a href="javascript:;" onclick="HaoAdmin.show('/edit/cache_empty')" class="btn btn-default pull-right" ><span class="glyphicon glyphicon-refresh"></span>清空缓存</a>
	<a href="javascript:;" onclick="HaoAdmin.show('/edit/cache_reset_stat')" class="btn btn-default pull-right" ><span class="glyphicon glyphicon-refresh"></span>重置统计</a>
</div>

<div class="table-responsive">
	<table class="table table-striped table-bordered table-hover">
		<thead>
			<th>参数</th>
			<th>含义</th>
			<th>值</th>
		</thead>
		<tbody>
	<?php
		$results = $requestResult->results;
		if (isset($results['keyspace_hits'],$results['keyspace_misses']))
		{
			print('<tr><td></td><td>缓存命中率</td><td>'.intval(100* $results['keyspace_hits']/($results['keyspace_hits']+$results['keyspace_misses'])).'%</td></tr>');
		}

		foreach ($results as $key=>$value)
		{
	?>
		<tr title="<?= getRedisInfoDesOfKey($key) ?>">
			<td><?= $key ?></td>
			<td><?= getRedisInfoTitleOfKey($key) ?></td>
			<td><?= $value ?></td>
		</tr>
	<?php
		}
	?>
		</tbody>
	</table>
</div>

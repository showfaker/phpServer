<?php
 
   return array(
        'APP_AUTOLOAD_PATH'=>'@.TagLib',
        'SESSION_AUTO_START'=>true,
        'USER_AUTH_ON'              =>true,
        'USER_AUTH_TYPE'			=>1,		// 默认认证类型 1 登录认证 2 实时认证
        'USER_AUTH_KEY'             =>'jinjiniao',	// 用户认证SESSION标记
        'ADMIN_AUTH_KEY'			=>'administrator',
        'USER_AUTH_MODEL'           =>'User',	// 默认验证数据表模型
        'AUTH_PWD_ENCODER'          =>'md5',	// 用户认证密码加密方式
        'USER_AUTH_GATEWAY'         =>'/Public/login',// 默认认证网关
        'NOT_AUTH_MODULE'           =>'Public,Xmtj,TimeTask',	// 默认无需认证模块
        'REQUIRE_AUTH_MODULE'       =>'',		// 默认需要认证模块
        'NOT_AUTH_ACTION'           =>'',		// 默认无需认证操作
        'REQUIRE_AUTH_ACTION'       =>'',		// 默认需要认证操作
        'GUEST_AUTH_ON'             =>false,    // 是否开启游客授权访问
        'GUEST_AUTH_ID'             =>0,        // 游客的用户ID
        'DB_LIKE_FIELDS'            =>'title|remark',
        'RBAC_ROLE_TABLE'           =>'think_role',
        'RBAC_USER_TABLE'           =>'think_role_user',
        'RBAC_ACCESS_TABLE'         =>'think_access',
        'RBAC_NODE_TABLE'           =>'think_node',
        'SHOW_PAGE_TRACE'           =>0,        //显示调试信息
		'VAR_PAGE' => 'p',
		
		'MAIL_ADDRESS'=>'noticeproject@163.com',// 邮箱地址 
		'MAIL_SMTP'=>'smtp.163.com',// 邮箱SMTP服务器 
		'MAIL_LOGINNAME'=>'noticeproject',// 邮箱登录帐号 
		'MAIL_PASSWORD'=>'VNJJOLUGTNASHHNU',// 邮箱密码jiuze9.com VNJJOLUGTNASHHNU
		'MAIL_CHARSET'=>'UTF-8',//编码           
		'MAIL_AUTH'=>true,//邮箱认证
		'MAIL_HTML'=>true,//true HTML格式 false TXT格式
		
		'TOKEN_ON'=>false,  // 是否开启令牌验证
		'TOKEN_NAME'=>'__hash__',    // 令牌验证的表单隐藏字段名称
		'TOKEN_TYPE'=>'md5',  //令牌哈希验证规则 默认为MD5
		'integrate'=>false,  // 是否集成
		
		'corpid'=>'ww08ba77946c3a7b2d',
        'appId'=>1000003,
        'corpsecret'=>'wbpekP7IX_HI4XBx9pyrtUvmKZ3evOJXTjMctdd4GhQ',
		
		'URL_MODEL'=>3, // 如果你的环境不支持PATHINFO 请设置为3
		'DB_TYPE'=>'mysql',
		'DB_HOST'=>'10.178.0.112',
		'DB_NAME'=>'projecttest',
		'DB_USER'=>'root',
		'DB_PWD'=>'serviceoa2013',
		'DB_PORT'=>'3306',
		'DB_PREFIX'=>'think_',
		'URL_CASE_INSENSITIVE'  => false,   // 默认false 表示URL区分大小写 true则表示不区分大小写
		'LOG_RECORD' => false, // 开启日志记录
		'LOG_LEVEL' => 'ERR,INFO',
    );

?>
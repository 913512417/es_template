-- 配置
CREATE TABLE v_system_conf (
    -- 配置id
    id                          INTEGER(11) 	    	NOT NULL	   AUTO_INCREMENT,
    -- 配置名称
    conf_name                   VARCHAR(100) 	    	NOT NULL       DEFAULT '',
    -- 配置别名
    conf_alias        		    VARCHAR(100)            NOT NULL       DEFAULT '',
    -- 值
    value	        		    TEXT          	        NOT NULL        DEFAULT '',
    -- 值的数据结构
    -- json  text
    value_type              VARCHAR(100)            NOT NULL       DEFAULT '',
    PRIMARY KEY (id),
    UNIQUE(conf_alias)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
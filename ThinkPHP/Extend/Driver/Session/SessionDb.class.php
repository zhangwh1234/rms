<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2012 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

defined('THINK_PATH') or exit();

/**
 * 数据库方式Session驱动
 *    CREATE TABLE think_session (
 *      session_id varchar(255) NOT NULL,
 *      session_expire int(11) NOT NULL,
 *      session_data blob,
 *      UNIQUE KEY `session_id` (`session_id`)
 *    );
 * @category   Extend
 * @package  Extend
 * @subpackage  Driver.Session
 * @author    liu21st <liu21st@gmail.com>
 */
class SessionDb
{

    /**
     * Session有效时间
     */
    protected $lifeTime = '';

    /**
     * session保存的数据库名
     */
    protected $sessionTable = '';

    /**
     * 数据库句柄
     */
    protected $hander = array();

    /**
     * 打开Session
     * @access public
     * @param string $savePath
     * @param mixed $sessName
     */
    public function open($savePath, $sessName)
    {
        $this->lifeTime = C('SESSION_EXPIRE') ? C('SESSION_EXPIRE') : ini_get('session.gc_maxlifetime');
        $this->sessionTable = C('SESSION_TABLE') ? C('SESSION_TABLE') : C("DB_PREFIX") . "session";
        //分布式数据库
        $host = explode(',', C('DB_HOST'));
        $port = explode(',', C('DB_PORT'));
        $name = explode(',', C('DB_NAME'));
        $user = explode(',', C('DB_USER'));
        $pwd = explode(',', C('DB_PWD'));

        $rmsDataPath = array();
        //在这里修改了数据库的配置路径，改为按访问的url来取得
        require APP_PATH . 'Conf/datapath.php';
        $HTTP_HOST = $_SERVER['HTTP_HOST'];
        $dbConfig = $rmsDataPath[$HTTP_HOST];
        $host = explode(',', $dbConfig['DB_HOST']);
        $port = explode(',', $dbConfig['DB_PORT']);
        $name = explode(',', $dbConfig['DB_NAME']);
        $user = explode(',', $dbConfig['DB_USER']);
        $pwd = explode(',', $dbConfig['DB_PWD']);

        if (1 == C('DB_DEPLOY_TYPE')) {
            //读写分离
            if (C('DB_RW_SEPARATE')) {
                $w = floor(mt_rand(0, C('DB_MASTER_NUM') - 1));
                if (is_numeric(C('DB_SLAVE_NO'))) {//指定服务器读
                    $r = C('DB_SLAVE_NO');
                } else {
                    $r = floor(mt_rand(C('DB_MASTER_NUM'), count($host) - 1));
                }
                //主数据库链接
                $hander = mysql_connect(
                    $host[$w] . (isset($port[$w]) ? ':' . $port[$w] : ':' . $port[0]),
                    isset($user[$w]) ? $user[$w] : $user[0],
                    isset($pwd[$w]) ? $pwd[$w] : $pwd[0]
                );
                $dbSel = mysql_select_db(
                    isset($name[$w]) ? $name[$w] : $name[0]
                    , $hander);
                if (!$hander || !$dbSel)
                    return false;
                $this->hander[0] = $hander;
                //从数据库链接
                $hander = mysql_connect(
                    $host[$r] . (isset($port[$r]) ? ':' . $port[$r] : ':' . $port[0]),
                    isset($user[$r]) ? $user[$r] : $user[0],
                    isset($pwd[$r]) ? $pwd[$r] : $pwd[0]
                );

                $dbSel = mysql_select_db(
                    isset($name[$r]) ? $name[$r] : $name[0]
                    , $hander);
                if (!$hander || !$dbSel)
                    return false;
                $this->hander[1] = $hander;
                return true;
            }
        }
        //从数据库链接
        $r = floor(mt_rand(0, count($host) - 1));
        $hander = mysql_connect(
            $host[$r] . (isset($port[$r]) ? ':' . $port[$r] : ':' . $port[0]),
            isset($user[$r]) ? $user[$r] : $user[0],
            isset($pwd[$r]) ? $pwd[$r] : $pwd[0]
        );
        $dbSel = mysql_select_db(
            isset($name[$r]) ? $name[$r] : $name[0]
            , $hander);
        if (!$hander || !$dbSel)
            return false;
        $this->hander = $hander;
        return true;
    }

    /**
     * 关闭Session
     * @access public
     */
    public function close()
    {
        if (is_array($this->hander)) {
            $this->gc($this->lifeTime);
            return (mysql_close($this->hander[0]) && mysql_close($this->hander[1]));
        }
        $this->gc($this->lifeTime);
        return mysql_close($this->hander);
    }

    /**
     * 读取Session
     * @access public
     * @param string $sessID
     */
    public function read($sessID)
    {
        $hander = is_array($this->hander) ? $this->hander[1] : $this->hander;
        $res = mysql_query("SELECT session_data AS data FROM " . $this->sessionTable . " WHERE session_id = '$sessID'   AND session_expire >" . time(), $hander);        //var_dump($res);
        if ($res) {
            $row = mysql_fetch_assoc($res);
            Log::write(MODULE_NAME.ACTINO_NAME.__LINE__.'READ的SQL：'.$row['data'], Log::SQL);
            return $row['data'];
        }
        return "";
    }

    /**
     * 写入Session
     * @access public
     * @param string $sessID
     * @param String $sessData
     */
    public function write($sessID, $sessData)
    {
        Log::write(MODULE_NAME . ACTION_NAME.__LINE__.'WRITE的SQL：'.$sessData, Log::SQL);
        $hander = is_array($this->hander) ? $this->hander[0] : $this->hander;
        $expire = time() + $this->lifeTime;
        $ip = get_client_ip();
        if (substr($ip, 0, 9) == '10.159.63') {
            return;  //这个内网地址，不需要session
        }
        if (strlen($sessData) == 0) {
            return;  //为零也不保存session
        }
        mysql_query("REPLACE INTO  " . $this->sessionTable . " (  session_id, session_expire, session_data,content)  VALUES( '$sessID', '$expire',  '$sessData', '$ip')", $hander);
        if (mysql_affected_rows($hander))
            return true;
        return false;
    }

    /**
     * 删除Session
     * @access public
     * @param string $sessID
     */
    public function destroy($sessID)
    {
        $hander = is_array($this->hander) ? $this->hander[0] : $this->hander;
        mysql_query("DELETE FROM " . $this->sessionTable . " WHERE session_id = '$sessID'", $hander);
        if (mysql_affected_rows($hander))
            return true;
        return false;
    }

    /**
     * Session 垃圾回收
     * @access public
     * @param string $sessMaxLifeTime
     */
    public function gc($sessMaxLifeTime)
    {
        $hander = is_array($this->hander) ? $this->hander[0] : $this->hander;
        mysql_query("DELETE FROM " . $this->sessionTable . " WHERE session_expire < " . time(), $hander);
        return mysql_affected_rows($hander);
    }

    /**
     * 打开Session
     * @access public
     */
    public function execute()
    {
        session_set_save_handler(array(&$this, "open"),
            array(&$this, "close"),
            array(&$this, "read"),
            array(&$this, "write"),
            array(&$this, "destroy"),
            array(&$this, "gc"));
    }
}

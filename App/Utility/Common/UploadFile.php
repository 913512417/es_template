<?php
namespace App\Utility\Common;
use EasySwoole\Utility\File;
class UploadFile
{
    /**
     * 基本文件存储根目录
     */
    protected $baseFile = EASYSWOOLE_ROOT.'/static/upload/';
    /**
     * 文件类
     * @var \EasySwoole\Http\Message\UploadFile
     */
    private $file;
    /**
     * 配置信息
     * @var array
     */
    private $config;
    /**
     * 错误信息
     * @var string
     */
    private $error;
    /**
     * 当前完整文件名
     * @var string
     */
    private $filename;
    /**
     * 上传文件名
     * @var string
     */
    private $saveName;

    public function __construct(\EasySwoole\Http\Message\UploadFile $file,$config,$baseFile = "")
    {
        $this->file = $file;
        if($baseFile){
            $this->baseFile = rtrim($baseFile,"/")."/";
        }
        $this->config = $config;
    }

    /**
     * 检测并上传
     * @return bool
     */
    public function checkAndUpload()
    {
        if(!$this->checkExt() || !$this->checkMediaType() || !$this->checkSize()) return false;
        $saveName = $this->buildSaveName();
        $filename = $this->baseFile.$saveName;
        if(!File::createDirectory(dirname($filename))) return false;
        $this->filename = $filename;
        $this->file->moveTo($this->filename);
        return true;
    }

    public function getFilename()
    {
        return $this->filename;
    }


    /**
     * 检测 文件后缀
     * @return bool
     */
    private function checkExt()
    {
        if(!isset($this->config["ext"])) return true;
        $ext = $this->config['ext'];
        if (is_string($ext)) {
            $ext = explode(',', $this->config['ext']);
        }
        $extension = strtolower(pathinfo($this->file->getClientFilename(), PATHINFO_EXTENSION));
        if (!in_array($extension, $ext)) {
            $this->error = '不允许扩展上传！';
            return false;
        }
        return true;
    }

    /**
     * 检测文件类型
     * @return bool
     */
    private function checkMediaType()
    {
        if(!isset($this->config["media_type"])) return true;
        $ext = $this->config['media_type'];
        if (is_string($ext)) {
            $ext = explode(',', $this->config['media_type']);
        }
        if (!in_array($this->file->getClientMediaType(), $ext)) {
            $this->error = '请上传正确文件类型！';
            return false;
        }
        return true;
    }

    /**
     * 检测文件类型
     * @return bool
     */
    private function checkSize()
    {
        if(!isset($this->config["size"])) return true;
        if($this->file->getSize() > $this->config['size']){
            $this->error = '文件大小超出限制！';
            return false;
        }
        return true;
    }

    /**
     * 检测文件目录是否存在 不存在择创建
     * @param $path
     * @return bool
     */
    private function checkPath($path)
    {
        if (is_dir($path)) {
            return true;
        }
        if (mkdir($path, 0755, true)) {
            return true;
        }
        $this->error = '文件目录创建失败';
        return false;
    }

    /**
     * 获取保存文件名
     * @return string
     */
    private function buildSaveName()
    {
        if(!isset($this->config["path"])){
            $this->config["path"] = "/";
        }
        $savename = trim($this->config['path'],'/').'/';
        $savename .= $this->autoBuildName();
        $savename .= '.'  . pathinfo($this->file->getClientFilename(), PATHINFO_EXTENSION);
        $this->saveName = $savename;
        return $savename;
    }
    /**
     * 获取上传文件的文件名
     * @return string
     */
    public function getSaveName(){
        return $this->saveName;
    }

    /**
     * 自动生成文件名
     * @return string
     */
    private function autoBuildName()
    {
        return date('Ymd').DIRECTORY_SEPARATOR.md5(microtime(true));
    }

    /**
     * 错误信息
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }


}
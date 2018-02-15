<?php 
/**
 * ====================================================================================
 *
 *
 * @author Emrul (https://lemexit.com)
 * @link https://lemexit.com 
 * @license https://lemexit.com/license
 * @package WittyLab
 * @subpackage API Handler
 */

use Aws\S3\S3Client;

class Upload{
	/**
	 * S3
	 * @var null
	 */
	protected $s3 = NULL;
	protected $bucket = NULL;
	/**
	 * [__construct description]
	 */
	public function __construct($region, $public, $private, $bucket){
		require(ROOT.'/includes/library/aws/aws-autoloader.php');
		$this->s3 = new S3Client(array(
													    'version' => 'latest',
													    'region'  => $region,
													    'credentials' => array(
													        'key'    => $public,
													        'secret' => $private,
													    ),
													    'scheme' => 'http'
											));
		$this->bucket = $bucket;
	}
	/**
	 * Upload
	 */
	public function save($name, $file){
    try {
				$upload = $this->s3->upload($this->bucket, $name, fopen($file, 'rb'), 'public-read');
				return $upload->get('ObjectURL');
		} catch(Exception $e) {
				return FALSE;
		}

	}
	public function delete($name){
		$name = explode("/{$this->bucket}/",$name);
		$name = $name[1];
		return $this->s3->deleteObject(array("Bucket" => $this->bucket, "Key" => $name));
	}
}
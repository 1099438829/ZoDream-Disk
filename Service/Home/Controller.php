<?php
namespace Service\Home;

use Zodream\Domain\Routing\Controller as BaseController;
use Zodream\Infrastructure\Config;
use Zodream\Infrastructure\Error\Error;
use Zodream\Infrastructure\Traits\AjaxTrait;

abstract class Controller extends BaseController {
	use AjaxTrait;
	protected $config = [];
	
	public function prepare() {
		$this->config = Config::getValue('disk');
		if (empty($this->config)) {
			Error::out('CONFIG ERROR!', __FILE__, __LINE__);
		}
	}
	
	public function ajaxFailure($error, $code = 1) {
		$this->ajaxReturn([
			'status' => 'failure',
			'error' => $error,
			'code' => $code
		]);
	}

	public function ajaxSuccess($data = null) {
		$this->ajaxReturn([
			'status' => 'success',
			'data' => $data
		]);
	}
}
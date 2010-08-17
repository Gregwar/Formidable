<?


class DSDError {
	public $name;
	public $msg;

	public function __construct($nm,$ms) {
		$this->name = $nm;
		$this->msg = $ms;
	}

	public function __toString() {
		return $this->msg;
	}
}

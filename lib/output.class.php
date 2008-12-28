<?
  class output {
    private $output;
    private $output_type;

    function __construct(&$output,$output_type = 'json') {

	$this->output = & $output;
	$this->output_type = $output_type;

    }

    function __destruct() {
	if ($this->output_type == 'json')
		echo json_encode($this->output);
	else if ($this->output_type == 'xml')
		echo xmlrpc_encode($this->output); # This produces less than helpful output, we may have to roll our own
	else echo "Unknown output type\n";
    }
  }

?>

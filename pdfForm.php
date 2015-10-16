<?php

class pdfForm {

    /*
     * Path to raw PDF form
     * @var string
     */
    private $pdfurl;
    
    /*
     * Form data
     * @var array
     */
    private $data;
    
    /*
     * Path to filled PDF form
     * @var string
     */
    private $output;
    
    /*
     * Flag for flattening the file
     * @var string
     */
    private $flatten;


    /**
     * Class Conctructor
     *
     * @param string $pdfurl
     * @param string $data
     */
    function __construct($pdfurl, $data) {
        $this->pdfurl = $pdfurl;
        $this->data   = $data;
    }

    /**
     * Save the file
     *
     * @param string $path
     */
    public function save($path = null) {
        if(is_null($path)) {
            return $this;
        }

        if(!$this->output) {
            $this->_generate();
        }

        $dest = pathinfo($path, PATHINFO_DIRNAME);
        if(!file_exists($dest)) {
            mkdir($dest, 0775, true);
        }

        copy($this->output, $path);
        unlink($this->output);

        $this->output = $path;

        return $this;
    }

    /**
     * Force download the filled PDF file
     *
     */
    public function download() {

        if(!$this->output) {
            $this->_generate();
        }

        $filepath = $this->output;
        if( file_exists($filepath) ) {        

            header('Content-Description: File Transfer');
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename=' . uniqid(gethostname()) . '.pdf' );
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filepath));

            readfile($filepath); 

            exit;
        }
    }

    /**
     * Generate a filled PDF file
     *
     */
    private function _generate() {

        $fdf = $this->make_fdf($this->data);         
        $this->output = $this->_tmpfile();
        exec("pdftk {$this->pdfurl} fill_form {$fdf} output {$this->output}{$this->flatten}");

        unlink($fdf);
    }

    /**
     * Extract fields information
     *
     * @param boolean $pretty
     * @return string
     */
    public function fields($pretty = false) {
        $tmp = $this->_tmpfile();

        exec("pdftk {$this->pdfurl} dump_data_fields > {$tmp}"); 
        $con  = file_get_contents($tmp);

        unlink($tmp);
        return ($pretty == true) ? nl2br($con) : $con;
    }

    /**
     * Generate FDF file
     * @param array $data
     * @return string
     */
    public function make_fdf($data) {

        $fdf = '%FDF-1.2
1 0 obj<</FDF<< /Fields[';

        foreach($data as $key => $value) {
            $fdf .= '<</T(' . $key . ')/V(' . $value . ')>>';
        }

        $fdf .= "] >> >>
endobj
trailer
<</Root 1 0 R>>
%%EOF";

        $fdf_file = $this->_tmpfile();
        file_put_contents($fdf_file, $fdf);     

        return $fdf_file;
    }

    /**
     * Set the flatten flag
     *
     * @return pdfWriter
     */
    public function flatten() {
        $this->flatten = ' flatten';
        return $this;
    }

    /**
     * Create a temporary file and return the name
     * 
     * @return string
     */
    private function _tmpfile() {
        return tempnam(sys_get_temp_dir(), gethostname());
    }
}
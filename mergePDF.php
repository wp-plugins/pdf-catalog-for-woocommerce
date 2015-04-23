<?php
if(!class_exists('mPDF'))
{
	include_once(dirname(__FILE__).'/pdf_data/mpdfxx/mpdf.php');
}
class mergePDF
{
    public function merge($inputFiles, $archivePath, $outputFile)
    {
        $zip = new ZipArchive;
        $zip->open($archivePath);
        ob_start();
            
        $html = ob_get_contents();
        
        $mpdf = new mPDF('utf-8'); 
        $mpdf->SetImportUse(); 
        foreach($inputFiles as $fk=>$f){
            for ($i=1; $i<=$pagecount = $mpdf->SetSourceFile( dirname(__FILE__).'/pdf_list/'.$f ); $i++){
                if($fk==1){
                    $tplId = $mpdf->ImportPage(1);
                }
                $tplId = $mpdf->ImportPage($i);
                $pgw = $mpdf->tpls[$tplId]['w'];
                $pgh = $mpdf->tpls[$tplId]['h'];
                
                if($pgw > $pgh){
                    $orientation = 'L';
                }else{
                    $orientation = 'P';
                }
                    
                $mpdf->AddPage($orientation); 
                $mpdf->UseTemplate($tplId); 
                $mpdf->WriteHTML($html);
            }
            
            unlink(dirname(__FILE__).'/pdf_list/'.$f);
        }
        
        $pdf = $mpdf->Output('', 'S');
        $ob = ob_get_contents(); 
        ob_end_clean();
        
        $zip->addFromString($outputFile, $pdf);
        $zip->close();
        
        return true;
    }
}
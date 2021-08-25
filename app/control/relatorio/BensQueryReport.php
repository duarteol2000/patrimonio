<?php
class BensQueryReport extends TPage
{
    private $form;
    
    public function __construct()
    {
        parent::__construct();
        
        $this->form = new BootstrapFormBuilder;
        $this->form->setFormTitle('Clientes');
        
        $combo_destino  = new TDBUniqueSearch('destino_id', 'patrimonio', 'Destino', 'id', 'nome');
        $output         = new TRadioGroup('output');
        
        $this->form->addFields( [new TLabel('Nome do Setor')], [$combo_destino] );
        $this->form->addFields( [new TLabel('Formato')], [$output] );
        
        $output->setUseButton();
        $combo_destino->setMinLength(1);
        
        $output->addItems( ['html' => 'HTML', 'pdf' => 'PDF', 'rtf' => 'RTF', 'xls' => 'XLS'] );
        $output->setValue( 'pdf' );
        $output->setLayout('horizontal');
        
        $this->form->addAction('Gerar', new TAction([$this, 'onGenerate']), 'fa:download blue');
        $this->form->addActionLink('Voltar', new TAction(array('BensList','onReload')),'fa:table blue');

        parent::add( $this->form );
    }
    
    public function onGenerate($param)
    {
        try
        {
            $conn = TTransaction::open('patrimonio');
            
            $data = $this->form->getData();
            

            
            
            if (!$data->destino_id){
                $sql = "SELECT m.destino_id,d.nome, m.bens_id,b.num_tombamento, b.descricao, e.estadoprod, m.ativo FROM movimentacao m 
                                    JOIN bens AS b ON m.bens_id = b.id  
                                    JOIN destino AS d ON m.destino_id = d.id 
                                    JOIN estadoprod as e ON b.estadoprod_id = e.id
                                    WHERE m.ativo = 'S' ORDER BY m.destino_id;";
                //echo'Entrou no primeiro....';                     
                $rows = TDatabase::getData( $conn, $sql, null );                    
            }else{
                $sql = "SELECT m.destino_id,d.nome, m.bens_id,b.num_tombamento, b.descricao, e.estadoprod, m.ativo FROM movimentacao m 
                                    JOIN bens AS b ON m.bens_id = b.id  
                                    JOIN destino AS d ON m.destino_id = d.id 
                                    JOIN estadoprod as e ON b.estadoprod_id = e.id
                                    WHERE m.ativo = 'S' and m.destino_id = :destino_id ORDER BY m.destino_id;";

                //echo'Entrou no segundo....';                                    
                $rows = TDatabase::getData( $conn, $sql, null, [ 'destino_id' => $data->destino_id ] );                                    
            
            }                        
            
            
            if ($rows)
            {
                //$widths = [40, 200, 80, 120, 80];
                $widths = [800, null, null,null, null];
                                
                switch ($data->output)
                {
                    case 'html':
                        $table = new TTableWriterHTML($widths);
                        break;
                    case 'pdf':
                        $table = new TTableWriterPDF($widths);
                        break;
                    case 'rtf':
                        $table = new TTableWriterRTF($widths);
                        break;
                    case 'xls':
                        $table = new TTableWriterXLS($widths);
                        break;
                }
                // id, nome, categoria, email, nascimento
            
                if (!empty($table))
                {
                    $table->addStyle('header', 'Helvetica', '16', 'B', '#ffffff', '#4B5D8E');
                    $table->addStyle('title',  'Helvetica', '10', 'B', '#ffffff', '#617FC3');
                    $table->addStyle('datap',  'Helvetica', '10', 'B',  '#000000', '#E3E3E3', 'LR');
                    $table->addStyle('datai',  'Helvetica', '10', '',  '#000000', '#ffffff', 'LR');
                    $table->addStyle('footer', 'Helvetica', '10', '',  '#2B2B2B', '#B4CAFF');
                }
                
                $table->setHeaderCallback( function($table) {
                    $table->addRow();
                    $table->addCell('Listagem de Bens Patrimoniais por Setor', 'center', 'header', 5);
                    
                    $table->addRow();
                    $table->addCell('Setor', 'left', 'title');
                    $table->addRow();
                    $table->addCell('Bem Patrimonial', 'left', 'title');
                });
                
                $table->setFooterCallback( function ($table) {
                    $table->addRow();
                    $table->addCell(date('d/m/Y H:i:s'), 'right', 'footer', 5);
                });
                
                
                
                $colore = true;
                $previous_dest = null;
                foreach ($rows as $row)
                {
                    //$style = $colore ? 'datap' : 'datai';
                    
                    if ($row['destino_id'] != $previous_dest) {  // checa se destino se é diferente do anterior
                        $contador=0;
                        $style = 'datap';
                        $table->addRow();
                        $table->addCell( $row['nome'], 'left', $style);
                        $style = 'datai';
                    }
                    $contador++;
                    $table->addRow();
                    $table->addCell( '     '.$contador.': '.$row['num_tombamento'].' - '.$row['descricao'].'   ('.$row['estadoprod'].')', 'left', $style);
                    
                    
                    $previous_dest = $row['destino_id'];     
                    $colore = !$colore;
                }
                
                $output = 'app/output/tabular.'.$data->output;
                
                if (!file_exists($output) OR is_writable($output))
                {
                    $table->save($output);
                    parent::openFile($output);
                    
                    new TMessage('info', 'Relatório gerado com sucesso');
                }
                else
                {
                    throw new Exception('Permissão negada: ' . $output);
                }
                
                return;
                
            }
            
            $this->form->setData($data);
            
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }
    function onshow()
    {
        parent::show();
    }
    
}
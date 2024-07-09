<?php //dd($ExcellDataArray);?>
<table>
    <thead>
        <tr>
            <th style="text-align: center; font-weight: bold;">Sr.No</th>
            <?php
                if(!empty($EventQuestionData)){
                   foreach($EventQuestionData as $res){
            ?>
                <th style="text-align: center; font-weight: bold;"><?php echo $res->question_label; ?></th>
            <?php        
                }
            } ?>
        </tr>
    </thead>
    <tbody>
           <?php $i=1;
                 //$url = env('APP_URL') . '/public/uploads/attendee_documents/';
                 foreach ($ExcellDataArray as $res): ?>
                <tr>
                   <td style="text-align: center;"><?php echo $i; ?></td>
                    <?php foreach ($EventQuestionData as $val): ?>
                        <?php
                            $answerValue = '';
                            $file_name = '';
                            foreach ($res as $answer) {
                                if ($answer->question_label == $val->question_label) {
                                    if($val->question_form_type == 'file'){
                                        $file_name = $answer->answer_value;  
                                    }else{
                                        $answerValue = $answer->answer_value;
                                    }
                                    break;
                                }
                            }
                        ?>
                        <?php 
                        if(isset($file_name) && !empty($file_name)){?>  
                            <td><a href="{{asset('uploads/attendee_documents/'.$file_name) }}" target="_blank"> <?php echo $file_name; ?> </a></td>
                        <?php }else{ ?>
                            <td><?= $answerValue ?></td>
                    <?php } endforeach; ?>
                </tr>
            <?php $i++; endforeach; ?>

    </tbody>
</table>
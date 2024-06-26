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
                 foreach ($ExcellDataArray as $res): ?>
                <tr>
                   <td style="text-align: center;"><?php echo $i; ?></td>
                    <?php foreach ($EventQuestionData as $val): ?>
                        <?php
                            $answerValue = '';
                            foreach ($res as $answer) {
                                if ($answer->question_label == $val->question_label) {
                                    $answerValue = $answer->answer_value;
                                    break;
                                }
                            }
                        ?>
                        <td><?= $answerValue ?></td>
                    <?php endforeach; ?>
                </tr>
            <?php $i++; endforeach; ?>

    </tbody>
</table>
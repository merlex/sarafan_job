<?php
$form=$this->form;
//$form= new Application_Form_Confirm();
$form->setAttrib(Application_Form_Confirm::attrSubmitText,'Опубликовать');
?>
<ul class="h clearfix path">
	<li><a href="../">Объявления</a>/</li>
	<li><a href="<?=$this->url('ad/edit/id/'.$this->obj->getID());?>"><?php echo $this->obj->title;?></a></li>
	<li><b>Публикация</b></li>
</ul>
<p class="message">Опубликовать запись "<?php echo $this->obj->title;?>"?</p>
<?php echo $form;?>
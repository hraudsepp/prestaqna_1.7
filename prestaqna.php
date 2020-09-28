<?php

if (!defined('_PS_VERSION_'))
	exit;

class PrestaQnA extends Module
{
	/* @var boolean error */
	protected $_errors = false;
	
	public function __construct()
	{
		$this->name = 'prestaqna';
		$this->tab = 'front_office_features';
		$this->version = '1.2.1';
		$this->author = 'Nemo';
		$this->need_instance = 0;
		$this->table_name = 'qna';

	 	parent::__construct();

		$this->displayName = $this->l('Q&As Block in product page');
		$this->description = $this->l('Adds a block to display the "ask a quick question" box in product page.');
		$this->confirmUninstall = $this->l('Are you sure you want to delete this module?');
	}
	
	public function install()
	{
		if (!parent::install() OR
			!$this->registerHook('header') OR
			!$this->registerHook('actionProductUpdate') OR
			!$this->registerHook('displayAdminProductsExtra') OR
			!$this->registerHook('productTab') OR
			!$this->registerHook('productTabContent') OR
			!$this->registerHook('displayBackOfficeHeader') OR
			!$this->_installTable()
			)
			return false;
		return true;
	}
	
	public function uninstall()
	{
		if (!parent::uninstall() OR !$this->_eraseTable())
			return false;
		return true;
	}


	/* Use this to create table if necessary. if not, erase it*/

	private function _installTable(){
		$sql = 'CREATE TABLE  `'._DB_PREFIX_.$this->table_name.'` (
				`id_qna` INT( 12 ) NOT NULL AUTO_INCREMENT,
				`question` TEXT NOT NULL ,
				`email` VARCHAR (64) NOT NULL ,
				`name` VARCHAR (64) NOT NULL ,
				`id_product` INT (12) NOT NULL ,
				`approved` INT (2) NOT NULL ,
				`date_added` DATE NOT NULL ,
				`answer` TEXT NOT NULL ,
				PRIMARY KEY (  `id_qna` )
				) ENGINE =' ._MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8';
		if (!Db::getInstance()->Execute($sql))
			return false;
		else return true;
	}

	/* Used in conjuction with the previous, delete if not necessary */
	private function _eraseTable(){
		if(!Db::getInstance()->Execute('DROP TABLE `'._DB_PREFIX_.$this->table_name.'`'))
			return false;
		else return true;
	}
	

	public function getContent(){

		global $cookie; // presta 0.4

		$this->_html .= '<h2>'.$this->displayName.'</h2>';

		if (Tools::isSubmit('submitApprove'))
		{
			$answers = Tools::getValue('answer');

			foreach ($answers as $id_qna => $content) {
				if(!Db::getInstance()->update($this->table_name, array('question' => pSQL($content['question']), 'answer' => pSQL($content['answer']), 'approved' => 1), 'id_qna = '.$id_qna))
					$this->_errors[] = $this->l('Error while approving id #').$id_qna.': '.mysql_error();
				else { //validated, send email to the customer
					if(!$this->sendEmail($content['email'], $content['id_product'], $content['pname']))
						$this->_errors[] = $this->l('Error while sending the email to').' '.$content['email'];
				}
			}
			if (!$this->_errors)
				$this->_html .= $this->displayConfirmation($this->l('Questions approved!'));
		}
		else if (Tools::isSubmit('reject'))
		{
			$email = Db::getInstance()->getValue('SELECT email FROM '._DB_PREFIX_.$this->table_name. ' WHERE id_qna ='.(int)(Tools::getValue('reject')));
			if(!Db::getInstance()->delete(_DB_PREFIX_.$this->table_name, 'id_qna ='.(int)(Tools::getValue('reject'))))
				$this->_errors[] = mysql_error();
			// else {
			// if(!Mail::Send($cookie->id_lang, 'qna_rejected', $this->l('Question rejected'),
			// 	array(),
			// 	$email, NULL, strval(Configuration::get('PS_SHOP_EMAIL')), strval(Configuration::get('PS_SHOP_NAME')), NULL, NULL, dirname(__FILE__).'/mails/', true))
			// 	$this->_errors[] = $this->l('Error while sending notification email');
			// }

			if (!$this->_errors)
				$this->_html .= $this->displayConfirmation($this->l('Question rejected!'));

		}
		if ($this->_errors) {
			$this->_html .= $this->displayError(implode('<br/>', $this->_errors));
		}

		$this->_displayForm();

		return	$this->_html;
	}
	
	private function _displayForm()
	{
		global $cookie, $currentIndex;

		$token = Tools::getValue('token');
		$thispage = $currentIndex.'&token='.$token.'&configure='.$this->name.'&module_name='.$this->name;

		$this->_html .= '<div id="fb-root"></div>
					<script>(function(d, s, id) {
					  var js, fjs = d.getElementsByTagName(s)[0];
					  if (d.getElementById(id)) return;
					  js = d.createElement(s); js.id = id;
					  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
					  fjs.parentNode.insertBefore(js, fjs);
					}(document, \'script\', \'facebook-jssdk\'));</script>';
		$this->_html .= '<p>'.$this->l('Provided for FREE by').' <a style="text-decoration:underline" target="_blank" href="http://store.nemops.com#fromqna" title="store.nemops.com!">store.nemops.com!</a> '.$this->l('Would you like to support Nemo\'s Post Scriptum to get more free modules?').'</p>';



		$this->_html .= '<div class="fb-like-box" style="float:left" data-href="https://www.facebook.com/pages/Nemos-Post-Scriptum/358370864236645" data-width="250" data-colorscheme="light" data-show-faces="false" data-header="false" data-stream="false" data-show-border="false"></div>';

		$this->_html .= '
			<div  style="float:left">

			<form style="text-align:right" action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
			<input type="hidden" name="cmd" value="_s-xclick">
			<input type="hidden" name="hosted_button_id" value="GSG68TUKQC24J">
			<input type="image" src="https://www.paypalobjects.com/en_US/IT/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
			<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
			</form>
			</div>';		


		$this->_html .= '<div class="clear">&nbsp;</div>';


		/* Stats - approved questions per product */



		if(version_compare(_PS_VERSION_, '1.5', '>'))	
			$approved = $this->getStats($this->context->language->id);
		else
			$approved = $this->getStats($cookie->id_lang);

		if ($approved && sizeof($approved)) {
			$this->_html .= '
				<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
					<fieldset><legend><img src="'.$this->_path.'logo.gif" alt="" title="" />'.$this->l('Stats').'</legend>
			';			
			foreach ($approved as $qna_n) {
				$this->_html .= '<p><strong><a href="index.php?controller=AdminProducts&key_tab=ModulePrestaqna&id_product='.$qna_n['id_product'].'&updateproduct&token='.Tools::getAdminTokenLite('AdminProducts').'" >'.$qna_n['name'].'</a>: </strong>'.$qna_n['count'].' '.$this->l('Q&As').'</p>';
			}
			$this->_html .= '
				</fieldset>
			</form><br />';

		} else $this->_html .= '<p>'.$this->l('No Questions answered yet').'</p>'; // END listing qnas


		/* Pending questions */

		if(version_compare(_PS_VERSION_, '1.5', '>'))	
			$pending = $this->listPending($this->context->language->id);
		else
			$pending = $this->listPending($cookie->id_lang);
		

		$this->_html .= '
			<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
				<fieldset><legend><img src="'.$this->_path.'logo.gif" alt="" title="" />'.$this->l('Pending Questions').'</legend>
		';

		if ($pending) {
			$this->_html .= '
			<table class="table">
				<thead>
					<tr><th>'.$this->l('Product').'</th><th>'.$this->l('Question').'</th><th>'.$this->l('By').'</th><th>'.$this->l('E-mail').'</th><th>'.$this->l('Answer').'</th><th></th></tr>
				</thead>
				<tbody>';

			foreach ($pending as $question) {
				$by = $question['name'] ? $question['name'] : $this->l('Guest');

				$this->_html .= '
					<tr>
						<td>
							<input type="hidden" name="answer['.$question['id_qna'].'][id_product]" value="'.$question['id_product'].'">
							<input type="hidden" name="answer['.$question['id_qna'].'][pname]" value="'.$question['pname'].'">
							'.$question['pname'].'
							</td>
						<td><textarea type="text" name="answer['.$question['id_qna'].'][question]" rows="5" cols="70">'.$question['question'].'</textarea></td>
						<td>'.$by.'</td>
						<td><input type="hidden" name="answer['.$question['id_qna'].'][email]" value="'.$question['email'].'">'.$question['email'].'</td>
						<td><textarea name="answer['.$question['id_qna'].'][answer]" rows="5" cols="70"></textarea></td>
						<td><a href="'.$thispage.'&reject='.$question['id_qna'].'" title="'.$this->l('Reject').'"><img src="../img/admin/delete.gif" alt="Delete"></a></td>
					</tr>
				';
			}
			$this->_html .= '
				</tbody>
			</table>';

			$this->_html .= '<p><input type="submit" class="button" name="submitApprove" value="'.$this->l('Approve the above Q&As').'"></p>';
			$this->_html .= '<div class="hint clear" style="display:block">'.$this->l('Notice: notifications will be sent to inquirers').'</div>';

		} else $this->_html .= '<p>'.$this->l('No pending questions').'</p>';

		$this->_html .= '
				</fieldset>
			</form>';

	}

	public function getStats($id_lang)
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
			SELECT q.id_product, pl.name, (SELECT COUNT(*) FROM  '._DB_PREFIX_.$this->table_name.' sq WHERE sq.id_product = q.id_product AND approved = 1)as count
			FROM '._DB_PREFIX_.$this->table_name.' q
			LEFT JOIN '._DB_PREFIX_.'product_lang pl ON (pl.id_product = q.id_product)
			WHERE approved = 1 
			AND pl.id_lang ='.$id_lang.'
			GROUP BY q.id_product, pl.name
 			ORDER BY q.id_product');
		    
	}

	public function listPending($id_lang)
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
			SELECT q.*, pl.name as pname
			FROM '._DB_PREFIX_.$this->table_name.' q
			LEFT JOIN '._DB_PREFIX_.'product_lang pl ON (pl.id_product = q.id_product)
			WHERE approved = 0
			AND pl.id_lang ='.$id_lang.' GROUP BY q.id_qna ORDER BY q.id_product');		
	}

	public function sendEmail($to, $id_product, $pname)
	{

		$link = new Link();
		$productLink = $link->getProductlink($id_product).'#qnaTab';

		if(version_compare(_PS_VERSION_, '1.5', '>'))
		{
			if(!Mail::Send($this->context->language->id, 'qna_answered', $this->l('An answer to you question is available'),
				array(
				'{link}' => $productLink,
				'{productname}' => $pname
					),
				$to, NULL, strval(Configuration::get('PS_SHOP_EMAIL')), strval(Configuration::get('PS_SHOP_NAME')), NULL, NULL, dirname(__FILE__).'/mails/'))
				return false;
			else return true;
		} else {
			global $cookie;
			if(!Mail::Send($cookie->id_lang, 'qna_answered', $this->l('An answer to you question is available'),
				array(
				'{link}' => $productLink,
				'{productname}' => $pname
					),
				$to, NULL, strval(Configuration::get('PS_SHOP_EMAIL')), strval(Configuration::get('PS_SHOP_NAME')), NULL, NULL, dirname(__FILE__).'/mails/'))
				return false;
			else return true;
		}
		
	}

	public function getQNAS($id_product)
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
			SELECT *
			FROM '._DB_PREFIX_.$this->table_name.' WHERE id_product = '.$id_product.' AND approved = 1');
		    
	}

	public static function sendRequest($postdata)
	{


		$message = $postdata['qna_q'];
		$name = $postdata['qna_name'];
		$email = $postdata['qna_email'];
		$product = $postdata['qna_prod'];

		if (!Validate::isMessage($message))
			return 'mex'; //invalid message
		if (!Validate::isGenericName($name))
			return 'name'; //invalid message
		if (!Validate::isEmail($email))
			return 'mail'; //invalid message	

		$data = array('question' => pSQL($message), 'email' => pSQL($email), 'name' => pSQL($name), 'approved' => 0, 'id_product' =>(int)$product, 'date_added' => date('Y-m-d'));
		$name = Product::getProductName((int)$product);

		if(!Db::getInstance()->insert('qna', $data))
			return 'err';

		if(version_compare(_PS_VERSION_, '1.5', '>'))
		{
			$context = Context::getContext();
			Mail::Send($context->language->id, 'qna_admin', Mail::l('New question to be moderated'),
				array(
					'{message}' => $message,
					'{product_name}' => $name
					),
				strval(Configuration::get('PS_SHOP_EMAIL')), NULL, strval(Configuration::get('PS_SHOP_EMAIL')), NULL, NULL, NULL, dirname(__FILE__).'/mails/');
			return true;	
		} else {
			
			global $cookie;
			Mail::Send($cookie->id_lang, 'qna_admin', Mail::l('New question to be moderated'),
				array(
					'{message}' => $message,
					'{product_name}' => $name
					),
				strval(Configuration::get('PS_SHOP_EMAIL')), NULL, strval(Configuration::get('PS_SHOP_EMAIL')), NULL, NULL, NULL, dirname(__FILE__).'/mails/');
			return true;	
		}
		

	}

	public function hookActionProductUpdate($params)
	{

		$questions = Tools::getValue('qnaquestion');
		if(!$questions)
			return;

		// remove any chosen entry
		if($to_delete = Tools::getValue('deleteqna'))
		{
			foreach ($to_delete as $qnadel) {
				unset($questions[$qnadel]);
				if(!Db::getInstance()->delete(_DB_PREFIX_.$this->table_name, 'id_qna ='.(int)$qnadel))
					$this->controller->errors[] = mysql_error();
			}
			
			
		}
		// now update each entry
		foreach ($questions as $id_qna => $content) {
			if(!Db::getInstance()->update($this->table_name, array('question' => pSQL($content['question']), 'answer' => pSQL($content['answer']), 'approved' => 1), 'id_qna = '.$id_qna))
				$this->controller->errors[] = $this->l('Error while approving id #').$id_qna.': '.mysql_error();
		}

	}

	public function hookDisplayAdminProductsExtra($params)
	{
		$qnas = $this->getQNAS((int)Tools::getValue('id_product'));


		$this->context->smarty->assign(array(
			'qnas'=> $qnas
		));

		return $this->display(__FILE__, 'prestaqna_bo.tpl');

	}
	
	public function hookProductTab($params)
	{
		if(version_compare(_PS_VERSION_, '1.6', '<'))
		return $this->display(__FILE__, 'prestaqna_tab.tpl');

		if(version_compare(_PS_VERSION_, '1.7', '>'))
		return $this->display(__FILE__, 'prestaqna_tab17.tpl');
	}
	
	public function hookProductTabContent($params)
	{

		$qnas = $this->getQNAS(Tools::getValue('id_product'));

		$base_dir = str_replace("http://", "https://", _PS_BASE_URL_ . __PS_BASE_URI__);

		if(version_compare(_PS_VERSION_, '1.5', '>'))
		{
			$this->context->smarty->assign(array(
				'qnas' => $qnas,
				'qnas_nb' => sizeof($qnas),
				'base_dir' => $base_dir
				));
		} else {
			global $smarty;
			$smarty->assign(array(
				'qnas' => $qnas,
				'qnas_nb' => sizeof($qnas)
				));
		}
		if(version_compare(_PS_VERSION_, '1.7', '>'))
			return $this->display(__FILE__, 'prestaqna_tab_content17.tpl');
		else if(version_compare(_PS_VERSION_, '1.6', '>'))
			return $this->display(__FILE__, 'prestaqna_tab_content.tpl');
		else return $this->display(__FILE__, 'prestaqna_tab_content15.tpl');
	}

	public function hookHeader($params)
	{
		if(version_compare(_PS_VERSION_, '1.5', '>'))
		{
			if(isset($this->context->controller->php_self)  && $this->context->controller->php_self == 'product')
			{
				$this->context->controller->addCSS(($this->_path).'css/prestaqna.css', 'all');		
				$this->context->controller->addJS(($this->_path).'js/jquery.validate.min.js', 'all');	
				$this->context->controller->addJS(($this->_path).'js/prestaqna.js', 'all');		
			}	
		} else {
			Tools::addCSS(($this->_path).'css/prestaqna.css', 'all');		
			Tools::addJS(($this->_path).'js/jquery.validate.min.js', 'all');	
			Tools::addJS(($this->_path).'js/prestaqna.js', 'all');		
		}
		
		
	}


	// DH42 support

	public function hookDisplayBackOfficeHeader($params)
	{

		if( isset($this->context->controller->dh_support) )
			return;

		$this->context->controller->dh_support = 1;
		if (version_compare(@_PS_VERSION_,'1.6','<'))
			$this->context->controller->addJS($this->_path . '/js/dh42_15.js', 'all');
		else
			$this->context->controller->addJS($this->_path . '/js/dh42.js', 'all');
		return;



	}
	
}

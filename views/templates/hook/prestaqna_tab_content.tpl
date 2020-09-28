<section class="page-propduct-box">



	<h3 id="#qnaTab" class="idTabHrefShort page-product-heading">{l s='Ask a question' mod='prestaqna'}</h3>
	<div id="qnaTab">
		<p class="centertext">
			<span><strong>{l s='NO registration required!' mod='prestaqna'}</strong></span>
		</p>
		{if $qnas_nb}<p id="qna_pointer"><strong>{$qnas_nb}</strong> {l s='Question(s) answered' mod='prestaqna'}</p>{/if}
		
		<p>{l s='If the question you have has not yet been answered here, use the form below to ask something about this addon.' mod='prestaqna'}</p>
		<form action="" method="POST" id="qna_ask" class="std box">
			

			<div class="field clearfix form-group">
				<label for="qna_q">{l s='I want to know:' mod='prestaqna'} </label>
				<textarea name="qna_q" id="qna_q" rows="8"/></textarea>
			</div>
			<div class="field clearfix form-group">
				<label for="qna_name">{l s='Name:' mod='prestaqna'} </label>
				<input type="text" name="qna_name" value="" id="qna_name" />
				<small>{l s='(optional)' mod='prestaqna'}</small>
			</div>
			<div class="field clearfix required form-group">
				<label for="qna_email">{l s='E-mail:' mod='prestaqna'} </label>
				<input type="text" name="qna_email" value="" id="qna_email" />
				<small><sup>*</sup>{l s='(Required to be notified when an answer is available)' mod='prestaqna'}</small>
			</div>

			<div class="form-group">
				<input type="hidden" name="qna_prod" value="{$smarty.get.id_product}"/>
				<a href="javascript:void(0)" title="{l s='Send question!' mod='prestaqna'}" class="btn btn-default" id="submitQNA">{l s='Send question!' mod='prestaqna'}</a>
			</div>
		</form>	

		{if isset($qnas) && $qnas}
			<div class="qna-answers">
				<ul>
					{foreach from=$qnas item=qna}
						<li>
								<span class="name">
									{l s='Asked by' mod='prestaqna'} {if empty($qna.name)}{l s='a guest' mod='prestaqna'}{else}{$qna.name|escape:'hmlall'}{/if} <br />
									{if $qna.date_added !="0000-00-00"}
										<em>{l s='on' mod='prestaqna'} {dateFormat date=$qna.date_added}</em>
									{/if}
								</span>
								<span class="question">{$qna.question|escape:'htmlall'}</span>
								<span class="answer">
									<strong>{l s='Answer:' mod='prestaqna'}</strong>	<br/>
									{$qna.answer|escape:'htmlall'}
								</span>

							<div style="clear:both"></div>	 
						</li>
					{/foreach}
				</ul>
			</div>
			
		{/if}
	</div>

	<script>
		var qna_error = "{l s='Whoops! It seems that your request could not be validated, please retry' mod='prestaqna'}",
			qna_badcontent = "{l s='Bad message content!' mod='prestaqna'}",
			qna_badname = "{l s='Bad name content!!' mod='prestaqna'}",
			qna_bademail = "{l s='Bad E-mail address!' mod='prestaqna'}",
			qna_confirm = "{l s='Thank you. Your question has been registered. You will be notified as soon as an answer is available' mod='prestaqna'}";

	</script>	
	
</section>

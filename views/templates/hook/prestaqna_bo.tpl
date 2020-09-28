<div id="product-seobooster" class="panel product-tab">
	<input type="hidden" name="submitted_tabs[]" value="ModulePrestaqna" />

	<h3>{l s='Ask a question' mod='prestaqna'}</h3>



	{if $qnas}


		
		<table class="table">
			<thead>
				<tr>
					<th>{l s='Question' mod='prestaqna'}</th>
					<th>{l s='By' mod='prestaqna'}</th>
					<th>{l s='E-mail' mod='prestaqna'}</th>
					<th>{l s='Answer' mod='prestaqna'}</th>
					<th>{l s='Delete' mod='prestaqna'}</th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$qnas item=qna}
					{if $qna.name}
						{assign var="p_name" value=$qna.name}
					{else}
						{assign var="p_name" value={l s='Guest' mod='prestaqna'}}
					{/if}
					<tr>
						<td><textarea type="text" name="qnaquestion[{$qna.id_qna}][question]" rows="5" cols="70">{$qna.question|escape:'htmlall'}</textarea></td>
						<td>{$p_name}</td>
						<td><input type="hidden" name="qnaquestion[{$qna.id_qna}][email]" value="{$qna.email}">{$qna.email}</td>
						<td><textarea name="qnaquestion[{$qna.id_qna}][answer]" rows="5" cols="70">{$qna.answer|escape:'htmlall'}</textarea></td>
						<td>
							<input type="checkbox" name="deleteqna[]" value="{$qna.id_qna}">
						</td>
					</tr>

				{/foreach}

			</tbody>
		</table>

	<div class="panel-footer">
		<a href="{$link->getAdminLink('AdminProducts')}" class="btn btn-default"><i class="process-icon-cancel"></i> {l s='Cancel'}</a>
		<button type="submit" name="submitAddproduct" class="btn btn-default pull-right"><i class="process-icon-save"></i> {l s='Save'}</button>
		<button type="submit" name="submitAddproductAndStay" class="btn btn-default pull-right"><i class="process-icon-save"></i> {l s='Save and stay'}</button>
	</div>
	{else}
	<p>{l s='No questions for this product' mod='prestaqna'}</p>
	{/if}
</div>


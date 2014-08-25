jQuery.noConflict();

jQuery(function()
{
	var typeSource = [], attrSource = ['product_id', 'stock', 'product'];

	function cloneFields(callback)
	{
		jQuery("#sitemap_fields").find('.clone_me').each(function ()
		{
			var clone = jQuery(this).parent('td').clone(),
				removeTag = '<td class="value"><a href="#" class="removeTag">Remove</div></td>',
				addTag    = '<td class="value"><a href="#" class="addTag">Add</div></td>';
			
			clone.find('.clone_me')
			.removeClass('clone_me')
			.addClass('cloned')
			.attr({
				'id'    : jQuery(this).attr('id').replace('code', 'value'),
				'name'  : jQuery(this).attr('name').replace('[code]', '[value]'),
				'value' : '{{'+jQuery(this).val()+'}}'
			});

			clone.find('.note').text('value');

			clone.insertAfter(jQuery(this).parent('td'));

			!jQuery(this).hasClass('default-tag') ? jQuery(this).parents('tr').append(removeTag).append(addTag) : '';
		});

		callback ? callback() : '';
	}

	function moveFields(callback)
	{
		jQuery("#sitemap_fields").find('.next_to_me').each(function ()
		{
			var moveMe = jQuery(this).parents('tr').next().find('.move_me').parent('td');
			var removeMe = moveMe.parent('tr'),
				removeTag = '<td class="value"><a href="#" class="removeTag">Remove</div></td>',
				addTag    = '<td class="value"><a href="#" class="addTag">Add</div></td>';

			moveMe.insertAfter(jQuery(this).parent('td'));
			removeMe.remove();
			!jQuery(this).hasClass('default-tag') ? jQuery(this).parents('tr').append(removeTag).append(addTag) : '';
		});

		callback ? callback() : '';
	}

	if(jQuery('body').hasClass("adminhtml-sitemaps-index"))
	{
		jQuery('.action-select').removeAttr('onchange');
		jQuery('.action-select').on('change', function (e)
		{
			e.preventDefault();

			if(jQuery('.action-select').find('option:last').is(':selected'))
			{
				if(confirm('Are you sure?'))
				{
					varienGridAction.execute(this);
				}
				else
				{
					jQuery('.action-select').find('option').removeAttr('selected');
					jQuery('.action-select').find('option:first').attr('selected', true);
					return false;
				}
			}
			else
			{
				varienGridAction.execute(this);
			}
		});
	}

	jQuery('#type_select').find('option').each(function ()
	{
		typeSource.push(jQuery.trim(jQuery(this).text()));
	});

	jQuery("#type").autocomplete({source: typeSource});

	jQuery('#attribute_select').find('option').each(function ()
	{
		attrSource.push('{{'+jQuery.trim(jQuery(this).text())+'}}');
	});

	if(!jQuery('body').hasClass('adminhtml-sitemaps-edit'))
	{
		cloneFields(function ()
		{
			jQuery("#sitemap_fields").on('click', '.removeTag', function (e)
			{
				e.preventDefault();

				confirm('Are you sure?') ? jQuery(this).parents('tr').remove() : '';
			});

			jQuery("#sitemap_fields").on('click', '.addTag', function (e)
			{
				e.preventDefault();

				var clone = jQuery("#sitemap_fields").find('tr:last').clone();
				var num = parseInt(jQuery.trim(clone.find('label').text().replace(/(.*)(\d)$/, '$2'))) + 1;
				var nameIndex = parseInt(clone.find('.clone_me').attr('title')) + 1;
				var newName = clone.find('.clone_me').attr('name').replace(/\d+/, nameIndex);

				clone.find('label').text('Custom TAG '+num);
				clone.find('input').attr({'name': newName, 'title': nameIndex});
				clone.find('.cloned').attr('name', clone.find('.cloned').attr('name').replace('[code]', '[value]'));
				clone.insertAfter(jQuery(this).parents('tr')).addClass('added').val();

				jQuery("#sitemap_fields").find('.custom-tag, .default-tag').autocomplete({source: attrSource});
			});

			jQuery("#sitemap_fields").find('.custom-tag, .default-tag').autocomplete({source: attrSource});
		});
	}
	else
	{
		moveFields(function ()
		{
			jQuery("#sitemap_fields").on('click', '.removeTag', function (e)
			{
				e.preventDefault();

				confirm('Are you sure?') ? jQuery(this).parents('tr').remove() : '';
			});

			jQuery("#sitemap_fields").on('click', '.addTag', function (e)
			{
				e.preventDefault();

				var clone = jQuery("#sitemap_fields").find('tr:last').clone();
				var num = parseInt(jQuery.trim(clone.find('label').text().replace(/(.*)(\d)$/, '$2'))) + 1;
				var nameIndex = parseInt(clone.find('.clone_me, .next_to_me').attr('title')) + 1;
				var newName = clone.find('.clone_me, .next_to_me').attr('name').replace(/\d+/, nameIndex);

				clone.find('label').text('Custom TAG '+num);
				clone.find('input').attr({'name': newName, 'title': nameIndex});
				clone.find('.move_me').attr('name', clone.find('.move_me').attr('name').replace('[code]', '[value]'));
				clone.insertAfter(jQuery(this).parents('tr')).addClass('added').val();

				jQuery("#sitemap_fields").find('.custom-tag, .default-tag').autocomplete({source: attrSource});
			});

			jQuery("#sitemap_fields").find('.custom-tag, .default-tag').autocomplete({source: attrSource});
		});
	}
});
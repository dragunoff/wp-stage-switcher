; (function ($, document, window) {
	'use strict';

	const $document = $(document);

	$document.ready(function ($) {
		// repeater
		const $table = $('.js-drgnff-env-table');
		const template = wp.template('drgnff-row');

		$('.js-drgnff-add-button').on('click', function (e) {
			e.preventDefault();

			const html = template({ index: $table.find('.js-drgnff-row').length });
			$table.append(html);
			$table.last().find('.js-drgnff-color-field').wpColorPicker();
		});

		$table.on('click', '.js-drgnff-row-remove', function (e) {
			e.preventDefault();

			$(this).parents('.js-drgnff-row').remove();

			updateIndexes($table);
		});

		// reset default environment
		$('.js-drgnff-default-reset').on('click', function (e) {
			e.preventDefault();

			$(this).parents('.js-drgnff-row').find('.js-drgnff-input:not(:disabled)').each(function () {
				const $input = $(this);
				const originalValue = $input.data('original-value');

				$input.val(originalValue);

				if ($input.hasClass('js-drgnff-color-field')) {
					$input.iris('color', originalValue);
				}
			});
		});

		// color picker init
		$('.js-drgnff-color-field').wpColorPicker();

		// initialize jquery ui sortable
		$('.js-drgnff-sortable').sortable({
			items: '.js-drgnff-row',
			handle: '.js-drgnff-sortable-handle',
			cursor: 'move',
			axis: 'y',
			update: function () {
				updateIndexes($table);
			}
		});
	});

	function updateIndexes($table) {
		$table.find('.js-drgnff-row').each(function (index) {
			$(this).find('.js-drgnff-input').each(function () {
				const $input = $(this);
				const name = $input.attr('name');

				$input.attr('name', name.replace(/\[\d+\]/, `[${index}]`));
			});
		});
	}

})(jQuery, document, window);


document.addEventListener("DOMContentLoaded", function(event) {
    jQuery(document.body).on('updated_wc_div', pwgc_bind_redeem_form);
    pwgc_bind_redeem_form();

    jQuery(document.body).on('updated_wc_div', pwgc_bind_remove_link);
    pwgc_bind_remove_link();

    jQuery('#pwgc-message').on('input propertychange', function() {
        pwgc_message_characters_remaining();
    });
    pwgc_message_characters_remaining();

    jQuery('form.variations_form').on('show_variation', function() {
        jQuery('#pwgc-purchase-container').show();
    });

    jQuery('form.variations_form').on('hide_variation', function() {
        jQuery('#pwgc-purchase-container').hide();
    });

    jQuery('#pwgc-to').on('blur', function() {
        var recipients = jQuery(this).val();
        if (recipients) {
            // For clarity, ensure we do a comma followed by a space.
            // Babel translation of this line:
            // jQuery(this).val(recipients.trim().split(/[ ,]+/).map(item=>item.trim()).join(', '));

            // This is compatible with IE11
            jQuery(this).val(recipients.trim().split(/[ ,]+/).map(function (item) {
                return item.trim();
            }).join(', '));
        }

        pwgc_toggle_quantity();
    });

    jQuery('.variations_form').on( 'found_variation.wc-variation-form', function() {
        pwgc_toggle_quantity();
    });

    jQuery('.variations_form').on( 'reset_data', function() {
        pwgc_toggle_quantity();
    });

    jQuery('.variations_form').on( 'submit', function(e) {
        if (jQuery('#pwgc-to').length) {
            var recipients = jQuery('#pwgc-to').val().split(/[ ,]+/);
            var badRecipients = [];

            for (var i = 0; i < recipients.length; i++) {
                if (!pwgc_is_email(recipients[i])) {
                    badRecipients.push(recipients[i]);
                }
            }

            if (badRecipients.length) {
                alert(pwgc.i18n.invalid_recipient_error + '\n\n' + badRecipients.join('\n'));
                e.preventDefault();
                return false;
            }
        }
    });

    jQuery('.show-pw-gift-card').off('click').on('click', function(e) {
        jQuery('.checkout_pw_gift_card').slideToggle(400, function() {
            jQuery('.checkout_pw_gift_card').find(':input:eq(0)').focus();
        });

        e.preventDefault();
        return false;
    });

    jQuery('#pwgc-apply-gift-card-checkout').off('click.pimwick').on('click.pimwick', function(e) {
        pwgc_checkout_redeem_gift_card(jQuery(this));
        e.preventDefault();
        return false;
    });

    jQuery('#pwgc-redeem-gift-card-number').off('keypress.pimwick').on('keypress.pimwick', function(e) {
        if (e.keyCode == 13) {
            pwgc_checkout_redeem_gift_card(jQuery('#pwgc-apply-gift-card-checkout'));

            e.preventDefault();
            return false;
        }
    });
});

function pwgc_is_email(email) {
    var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    return regex.test(email);
}

function pwgc_toggle_quantity() {
    if (jQuery('#pwgc-to').length) {
        var recipients = jQuery('#pwgc-to').val().split(/[ ,]+/);
        if (recipients.length > 1) {
            jQuery('#pwgc-recipient-count').text(recipients.length);
            jQuery('#pwgc-quantity-one-per-recipient').show();
            jQuery('input.qty').val('1');
            jQuery('.quantity').hide();
        } else {
            jQuery('#pwgc-quantity-one-per-recipient').hide();
            jQuery('.quantity').show();
        }
    }
}

function pwgc_bind_remove_link() {
    jQuery('.pwgc-remove-card').on('click', function(e) {
        var cardNumber = jQuery(this).attr('data-card-number');

        jQuery.post(pwgc.ajaxurl, {'action': 'pw-gift-cards-remove', 'card_number': cardNumber, 'security': pwgc.nonces.remove_card}, function( result ) {
            location.reload();
        }).fail(function(xhr, textStatus, errorThrown) {
            if (errorThrown) {
                alert(errorThrown);
            } else {
                alert('Unknown Error');
            }
            location.reload();
        });

        e.preventDefault();
        return false;
    });
}

function pwgc_bind_redeem_form() {
    jQuery('#pwgc-redeem-form').off('submit.pimwick').on('submit.pimwick', function(e) {
        var redeemButton = jQuery('#pwgc-redeem-button');

        pwgc_redeem_gift_card(redeemButton);

        e.preventDefault();
        return false;
    });

    jQuery('#pwgc-apply-gift-card,#pwgc-redeem-button').off('click.pimwick').on('click.pimwick', function(e) {
        pwgc_redeem_gift_card(jQuery(this));

        e.preventDefault();
        return false;
    });

    jQuery('#pwgc-redeem-gift-card-number').off('keypress.pimwick').on('keypress.pimwick', function(e) {
        if (e.keyCode == 13) {
            pwgc_redeem_gift_card(jQuery('#pwgc-apply-gift-card'));

            e.preventDefault();
            return false;
        }
    });
}

function pwgc_redeem_gift_card(redeemButton) {
    var cardNumber = jQuery('#pwgc-redeem-gift-card-number');
    var errorContainer = jQuery('#pwgc-redeem-error');

    errorContainer.text('');
    redeemButton.attr('data-apply-text', redeemButton.attr('value')).attr('value', redeemButton.attr('data-wait-text')).prop('disabled', true);

    jQuery.post(pwgc.ajaxurl, {'action': 'pw-gift-cards-redeem', 'card_number': cardNumber.val(), 'security': pwgc.nonces.apply_gift_card}, function( result ) {
        if (result.success) {
            // We could hook into the cart's ajax calls, but for now we'll just reload.
            location.reload();
        } else {
            errorContainer.text(result.data.message);
            redeemButton.attr('value', redeemButton.attr('data-apply-text')).prop('disabled', false);
            cardNumber.focus();
        }
    }).fail(function(xhr, textStatus, errorThrown) {
        if (errorThrown) {
            errorContainer.text(errorThrown);
        } else {
            errorContainer.text('Unknown Error');
        }
        redeemButton.attr('value', redeemButton.attr('data-apply-text')).prop('disabled', false);
        cardNumber.focus();
    });
}

function pwgc_message_characters_remaining() {
    var charsRemaining = pwgc.max_message_characters;

    var messageElement = jQuery('#pwgc-message').val();
    if (messageElement) {
        charsRemaining -= messageElement.length;
    }

    jQuery('#pwgc-message-characters-remaining').text(charsRemaining);
}

function pwgc_checkout_redeem_gift_card(redeemButton) {
    var errorContainer = jQuery('#pwgc-redeem-error');
    var cardNumber = jQuery('#pwgc-redeem-gift-card-number');

    errorContainer.text('');
    redeemButton.attr('data-apply-text', redeemButton.attr('value')).attr('value', redeemButton.attr('data-wait-text')).prop('disabled', true);

    jQuery.post(pwgc.ajaxurl, {'action': 'pw-gift-cards-redeem', 'card_number': cardNumber.val(), 'security': pwgc.nonces.apply_gift_card}, function( result ) {
        if (result.success) {
            // We could hook into the cart's ajax calls, but for now we'll just reload.
            location.reload();
        } else {
            errorContainer.text(result.data.message);
            redeemButton.attr('value', redeemButton.attr('data-apply-text')).prop('disabled', false);
            cardNumber.focus();
        }
    }).fail(function(xhr, textStatus, errorThrown) {
        if (errorThrown) {
            errorContainer.text(errorThrown);
        } else {
            errorContainer.text('Unknown Error');
        }
        redeemButton.attr('value', redeemButton.attr('data-apply-text')).prop('disabled', false);
        cardNumber.focus();
    });
}

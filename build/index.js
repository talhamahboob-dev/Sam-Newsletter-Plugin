/**
 * Gutenberg Block for SAM Newsletter
 */

const { registerBlockType } = wp.blocks;
const { createElement: el } = wp.element;
const { __ } = wp.i18n;

registerBlockType('sam-newsletter/subscribe-form', {
    title: __('SAM Newsletter', 'sam-newsletter'),
    description: __('A newsletter subscription form with name and email fields.', 'sam-newsletter'),
    icon: 'email-alt',
    category: 'common',
    keywords: [__('newsletter', 'sam-newsletter'), __('email', 'sam-newsletter'), __('subscribe', 'sam-newsletter')],
    
    attributes: {
        placeholder: {
            type: 'string',
            default: ''
        }
    },
    
    edit: function(props) {
        return el(
            'div',
            { className: 'sam-newsletter-block-editor' },
            el(
                'div',
                { className: 'sam-newsletter-preview' },
                el('div', { className: 'sam-newsletter-icon' }, '📧'),
                el('h3', {}, __('SAM Newsletter Form', 'sam-newsletter')),
                el('p', {}, __('This block will display a newsletter subscription form with name and email fields on the frontend.', 'sam-newsletter')),
                el(
                    'div',
                    { className: 'sam-newsletter-preview-form' },
                    el(
                        'div',
                        { className: 'preview-field' },
                        el('label', {}, __('Name', 'sam-newsletter')),
                        el('input', { 
                            type: 'text', 
                            placeholder: __('Enter your name', 'sam-newsletter'),
                            disabled: true 
                        })
                    ),
                    el(
                        'div',
                        { className: 'preview-field' },
                        el('label', {}, __('Email', 'sam-newsletter')),
                        el('input', { 
                            type: 'email', 
                            placeholder: __('Enter your email', 'sam-newsletter'),
                            disabled: true 
                        })
                    ),
                    el(
                        'button',
                        { 
                            className: 'preview-button',
                            disabled: true 
                        },
                        __('Subscribe', 'sam-newsletter')
                    )
                )
            )
        );
    },
    
    save: function() {
        // Dynamic block - rendered by PHP
        return null;
    }
});
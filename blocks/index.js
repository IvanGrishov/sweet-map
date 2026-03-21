(function (blocks, element, blockEditor, components, i18n) {
  'use strict';

  var el = element.createElement;
  var __ = i18n.__;
  var InspectorControls = blockEditor.InspectorControls;
  var PanelBody = components.PanelBody;
  var SelectControl = components.SelectControl;

  blocks.registerBlockType('sweet-map/map', {
    title: 'Sweet Map',
    description: __('Add an interactive map to your page', 'sweet-map'),
    category: 'embed',
    icon: 'location',
    keywords: ['map', 'sweet map', 'leaflet'],
    attributes: {
      mapId: {
        type: 'string',
        default: 'default',
      },
    },

    edit: function (props) {
      var data = window.swmapBlockData || {};
      var maps = data.maps || ['default'];
      var mapId = props.attributes.mapId || 'default';

      var options = maps.map(function (id) {
        return { label: id === 'default' ? id + ' (main)' : id, value: id };
      });

      return [
        el(
          InspectorControls,
          { key: 'inspector' },
          el(
            PanelBody,
            { title: __('Map', 'sweet-map'), initialOpen: true },
            el(SelectControl, {
              label: __('Select map', 'sweet-map'),
              value: mapId,
              options: options,
              onChange: function (val) {
                props.setAttributes({ mapId: val });
              },
            })
          )
        ),
        el(
          'div',
          {
            key: 'preview',
            style: {
              display: 'flex',
              flexDirection: 'column',
              alignItems: 'center',
              justifyContent: 'center',
              gap: '12px',
              minHeight: '200px',
              background: 'linear-gradient(135deg, #f0f4ff 0%, #e8f4f8 100%)',
              border: '2px dashed #818cf8',
              borderRadius: '8px',
              padding: '32px',
            },
          },
          el('span', {
            className: 'dashicons dashicons-location',
            style: { fontSize: '48px', width: '48px', height: '48px', color: '#4f46e5' },
          }),
          el('strong', { style: { fontSize: '18px', color: '#1e293b' } }, 'Sweet Map'),
          el(
            'div',
            { style: { display: 'flex', alignItems: 'center', gap: '8px' } },
            el('span', { style: { color: '#64748b', fontSize: '13px' } }, __('Map:', 'sweet-map') + ' '),
            el(
              'code',
              {
                style: {
                  background: '#e0e7ff',
                  color: '#4f46e5',
                  padding: '2px 10px',
                  borderRadius: '4px',
                  fontSize: '13px',
                  fontWeight: '600',
                },
              },
              mapId
            )
          ),
          el(
            'span',
            { style: { color: '#94a3b8', fontSize: '12px' } },
            __('The map will appear on your page', 'sweet-map')
          )
        ),
      ];
    },

    save: function () {
      return null; // dynamic block — PHP renders
    },
  });
})(window.wp.blocks, window.wp.element, window.wp.blockEditor, window.wp.components, window.wp.i18n);

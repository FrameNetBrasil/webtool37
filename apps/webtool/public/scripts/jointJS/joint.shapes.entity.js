joint.shapes.entity = {};

joint.shapes.entity.Frame = joint.shapes.basic.Rect.extend({
    markup: '<g class="rotatable"><g class="scalable"><rect/></g><text/></g>',
    defaults: joint.util.deepSupplement({
        type: 'entity.Frame',
        attrs: {
            'rect': {fill: '#FFFFFF', originalFill: '#FFFFFF', stroke: 'black', width: 100, height: 60},
            'text': {'font-size': 14, text: '', 'ref-x': .5, 'ref-y': .5, ref: 'rect', 'y-alignment': 'middle', 'x-alignment': 'middle', fill: 'black', 'font-family': 'Arial, helvetica, sans-serif'}
        }

    }, joint.shapes.basic.Rect.prototype.defaults)
});

joint.shapes.entity.FrameElement = joint.shapes.basic.Generic.extend(_.extend({}, joint.shapes.basic.PortsModelInterface, {
    markup: '<g class="rotatable"><g class="scalable"><rect/></g><text class="flabel"/><g class="inPorts"/><g class="outPorts"/></g>',
    portMarkup: '<g class="port<%= id %>"><circle/><text/></g>',
    defaults: joint.util.deepSupplement({
        type: 'entity.FrameElement',
        size: {width: 1, height: 1},
        inPorts: [],
        outPorts: [],
        attrs: {
            '.': {magnet: false},
            rect: {
                width: 150, height: 250,
                stroke: 'black'
            },
            circle: {
                r: 10,
                magnet: true,
                stroke: 'black'
            },
            text: {
                fill: 'black',
                'pointer-events': 'none'
            },
            '.inPorts circle': {fill: '#16A085'},
            '.outPorts circle': {fill: '#E74C3C'},
            '.inPorts text': {x: 20, dy: -4},
            '.outPorts text': {x: -20, dy: -4, 'text-anchor': 'end'}
        }

    }, joint.shapes.basic.Generic.prototype.defaults),
    getPortAttrs: function(portName, index, total, selector, type) {

        var attrs = {};

        var portClass = 'port' + index;
        var portSelector = selector + '>.' + portClass;
        var portTextSelector = portSelector + '>text';
        var portCircleSelector = portSelector + '>circle';

        attrs[portTextSelector] = {text: portName.name};
        attrs[portCircleSelector] = {port: {id: portName.idEntity || _.uniqueId(type), type: type}};
        attrs[portSelector] = {ref: 'rect', 'ref-y': (index + 0.5) * (1 / total)};

        if (selector === '.outPorts') {
            attrs[portSelector]['ref-dx'] = 0;
        }

        return attrs;
    }
}));

joint.shapes.entity.Cxn = joint.shapes.basic.Rect.extend({
    markup: '<g class="rotatable"><g class="scalable"><rect/></g><text/></g>',
    defaults: joint.util.deepSupplement({
        type: 'entity.Cxn',
        attrs: {
            'rect': {fill: '#FFFFFF', originalFill: '#FFFFFF', stroke: 'black', width: 100, height: 60},
            'text': {'font-size': 14, text: '', 'ref-x': .5, 'ref-y': .5, ref: 'rect', 'y-alignment': 'middle', 'x-alignment': 'middle', fill: 'black', 'font-family': 'Arial, helvetica, sans-serif'}
        }

    }, joint.shapes.basic.Rect.prototype.defaults)
});

joint.shapes.entity.CxnElement = joint.shapes.basic.Generic.extend(_.extend({}, joint.shapes.basic.PortsModelInterface, {
    markup: '<g class="rotatable"><g class="scalable"><rect/></g><text class="flabel"/><g class="inPorts"/><g class="outPorts"/></g>',
    portMarkup: '<g class="port<%= id %>"><circle/><text/></g>',
    defaults: joint.util.deepSupplement({
        type: 'entity.CxnElement',
        size: {width: 1, height: 1},
        inPorts: [],
        outPorts: [],
        attrs: {
            '.': {magnet: false},
            rect: {
                width: 150, height: 250,
                stroke: 'black'
            },
            circle: {
                r: 10,
                magnet: true,
                stroke: 'black'
            },
            text: {
                fill: 'black',
                'pointer-events': 'none'
            },
            '.inPorts circle': {fill: '#16A085'},
            '.outPorts circle': {fill: '#E74C3C'},
            '.inPorts text': {x: 20, dy: -4},
            '.outPorts text': {x: -20, dy: -4, 'text-anchor': 'end'}
        }

    }, joint.shapes.basic.Generic.prototype.defaults),
    getPortAttrs: function(portName, index, total, selector, type) {

        var attrs = {};

        var portClass = 'port' + index;
        var portSelector = selector + '>.' + portClass;
        var portTextSelector = portSelector + '>text';
        var portCircleSelector = portSelector + '>circle';

        attrs[portTextSelector] = {text: portName.name};
        attrs[portCircleSelector] = {port: {id: portName.idEntity || _.uniqueId(type), type: type}};
        attrs[portSelector] = {ref: 'rect', 'ref-y': (index + 0.5) * (1 / total)};

        if (selector === '.outPorts') {
            attrs[portSelector]['ref-dx'] = 0;
        }

        return attrs;
    }
}));


joint.shapes.entity.Relation = joint.dia.Link.extend({
    defaults: joint.util.deepSupplement({
        type: 'entity.Relation',
        attrs: {
            '.marker-target': {d: 'M 10 0 L 0 5 L 10 10 z'}
        },
        toolMarkup: [
            '<g class="link-tool">',
            '  <g class="tool-remove" event="remove">',
            '    <circle r="11" />',
            '    <path transform="scale(.8) translate(-16, -16)" d="M24.778,21.419 19.276,15.917 24.777,10.415 21.949,7.585 16.447,13.087 10.945,7.585 8.117,10.415 13.618,15.917 8.116,21.419 10.946,24.248 16.447,18.746 21.948,24.248z"/>',
            '    <title>Remove relation</title>',
            '  </g>',
            '  <g event="link:options">',
//            '<circle r="11" transform="translate(25)"/>',
            '    <path fill="black" transform="scale(.55) translate(29, -16)" d="M31.229,17.736c0.064-0.571,0.104-1.148,0.104-1.736s-0.04-1.166-0.104-1.737l-4.377-1.557c-0.218-0.716-0.504-1.401-0.851-2.05l1.993-4.192c-0.725-0.91-1.549-1.734-2.458-2.459l-4.193,1.994c-0.647-0.347-1.334-0.632-2.049-0.849l-1.558-4.378C17.165,0.708,16.588,0.667,16,0.667s-1.166,0.041-1.737,0.105L12.707,5.15c-0.716,0.217-1.401,0.502-2.05,0.849L6.464,4.005C5.554,4.73,4.73,5.554,4.005,6.464l1.994,4.192c-0.347,0.648-0.632,1.334-0.849,2.05l-4.378,1.557C0.708,14.834,0.667,15.412,0.667,16s0.041,1.165,0.105,1.736l4.378,1.558c0.217,0.715,0.502,1.401,0.849,2.049l-1.994,4.193c0.725,0.909,1.549,1.733,2.459,2.458l4.192-1.993c0.648,0.347,1.334,0.633,2.05,0.851l1.557,4.377c0.571,0.064,1.148,0.104,1.737,0.104c0.588,0,1.165-0.04,1.736-0.104l1.558-4.377c0.715-0.218,1.399-0.504,2.049-0.851l4.193,1.993c0.909-0.725,1.733-1.549,2.458-2.458l-1.993-4.193c0.347-0.647,0.633-1.334,0.851-2.049L31.229,17.736zM16,20.871c-2.69,0-4.872-2.182-4.872-4.871c0-2.69,2.182-4.872,4.872-4.872c2.689,0,4.871,2.182,4.871,4.872C20.871,18.689,18.689,20.871,16,20.871z"/>',
            '    <title>Elements</title>',
            '  </g>',
            '</g>'
        ].join(''),
        smooth: true
    }, joint.dia.Link.prototype.defaults)
});

joint.shapes.entity.SimpleRelation = joint.dia.Link.extend({
    defaults: joint.util.deepSupplement({
        type: 'entity.SimpleRelation',
        attrs: {
            '.marker-target': {d: 'M 10 0 L 0 5 L 10 10 z'}
        },
        toolMarkup: [
            '<g class="link-tool">',
            '  <g class="tool-remove" event="remove">',
            '    <circle r="11" />',
            '    <path transform="scale(.8) translate(-16, -16)" d="M24.778,21.419 19.276,15.917 24.777,10.415 21.949,7.585 16.447,13.087 10.945,7.585 8.117,10.415 13.618,15.917 8.116,21.419 10.946,24.248 16.447,18.746 21.948,24.248z"/>',
            '    <title>Remove relation</title>',
            '  </g>',
            '</g>'
        ].join(''),
        smooth: true
    }, joint.dia.Link.prototype.defaults)
});

joint.shapes.entity.RelationView = joint.dia.LinkView.extend({
    pointerdown: function(evt) {
        joint.dia.LinkView.prototype.pointerdown.apply(this, arguments);
    }
});

joint.shapes.entity.SimpleRelationView = joint.dia.LinkView.extend({
    pointerdown: function(evt) {
        joint.dia.LinkView.prototype.pointerdown.apply(this, arguments);
    }
});

joint.shapes.entity.FrameElementView = joint.dia.ElementView.extend(joint.shapes.basic.PortsViewInterface);
joint.shapes.entity.CxnElementView = joint.dia.ElementView.extend(joint.shapes.basic.PortsViewInterface);


if (typeof exports === 'object') {

    module.exports = joint.shapes.entity;
}

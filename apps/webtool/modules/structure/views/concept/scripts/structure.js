<script type="text/javascript">

var structure = {
    app: {{$manager->getApp()}},
    isMaster: true,
    node: null
};
$(function () {
    $('#structureLayout').layout({
        fit:true
    });

    structure.newConcept = function () {
        var nodeId = structure.node.id;
        manager.doGet({{$manager->getURL('structure/concept/formNewConcept')}} + '/' + nodeId,'structureCenterPane');
    }

    structure.editConcept = function (id) {
        if ($.type(id) === "undefined") {
            id = structure.node.id.substr(1);
        }
        manager.doGet({{$manager->getURL('structure/concept/formUpdateConcept')}} + '/' + id,'structureCenterPane');
    }

    structure.delConcept = function (id) {
        if ($.type(id) === "undefined") {
            id = structure.node.id.substr(1);
        }
        manager.doGet({{$manager->getURL('structure/concept/deleteConcept')}} + '/' + id,'structureCenterPane');
    }

    structure.delConceptElement = function () {
        var nodeId = structure.node.id;
        manager.doGet({{$manager->getURL('structure/concept/deleteConceptElement')}} + '/' + nodeId,'structureCenterPane');
    }

    structure.reloadConcept = function () {
        $('#structureCenterPane').html('');
        $('#conceptsTree').tree('reload');
    }

    structure.addConceptElement = function () {
        var id = structure.node.id.substr(1);
        $('#concept_idConceptElement').val(id);
        $('#formAddConceptElement').dialog({
            closed: false
        });
        $('#formAddConceptElement').dialog('resize',{
            width:'auto',
            height:'auto'
        });
        $('#formAddConceptElement').dialog({'title':'Add Element to ' + structure.node.text});
        $('#formAddConceptElement').dialog('doLayout');
        $('#formAddConceptElement').dialog('open');
    }

    structure.formAddConceptElementSave = function () {
        $('#formAddConceptElementForm').submit();
    }

    structure.contextMenuConcept = function(e, node) {
        if (!structure.isMaster) {
            return;
        }
        e.preventDefault();
        console.log(node);
        structure.node = node;
        var $menu = '';
        $(this).tree('select',node.target);
        if (node.id == 'root') {
            $menu = $('#menuRootConcepts');
        } else if (node.id.charAt(0) == 'c') {
            $menu = $('#menuConcept');
        } else if (node.id.charAt(0) == 'e') {
            $menu = $('#menuConceptElement');
        }
        if ($menu != '') {
            $menu.menu('show',{
                left: e.pageX,
                top: e.pageY
            });
        }
    }

});

</script>
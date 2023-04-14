<div id="grapherLayout" style="width:100%;height:100%;">
    <div data-options="region:'north',title:'Grapher: Qualia'">
    </div>
    <div data-options="region:'west',split:true,title:'LU'" style="width:280px;padding:10px;">
        <input id="lu" name="lu" type="text" style="width:200px; padding:5px">
        <ul id="luTree"></ul>
    </div>
    <div id="grapherCenterPane" data-options="region:'center'" style="width: 100%">
        <div id="grapherPanel" style="height: 95%">
            <div id="grapherToolbar">
                <div id="grToolBar" class="datagrid-toolbar">
                    <form id="formGrapher" name="formGrapher">
                        <input type="hidden" id="rt" name="rt"/>
                        <a id="btnReload" href="#">Reload last</a>
                    </form>
                </div>
            </div>
            <div id="output_graph_content" style="position:relative; width:100%; height:670px; overflow-y: scroll"></div>
        </div>
    </div>
</div>

<style>
    svg {
        width: 100%;
        height:100%;
    }
    text {
        font-family: "IBM Plex Sans";
        font-size: 8px;
        cursor: pointer;
    }
</style>
<script type="text/javascript">
    {!! $view->includeFile('grapher_qualia.js') !!}
</script>

<script type="text/javascript">

    $(function () {
        $('#grapherLayout').layout({
            fit:true
        });
        //grapher.instance = new grapher.graph("grapherArea");

        grapher.reload = function () {
            $('#grapherCenterPane').html('');
            $('#luTree').tree('reload');
        }

        grapher.show = function (idEntity) {
            manager.doAjax("{{$manager->getURL('api/grapherqualia/getRelations')}}", function(data) {
                console.log(data);
                let dataObject = JSON.parse(data);
                $( "#output_graph_content" ).remove( "svg" );
                grapher.init();
                grapher.convert(dataObject);
                grapher.render();
            }, {idEntity: idEntity});
        }

        grapher.add = function (idEntity) {
            manager.doAjax("{{$manager->getURL('api/grapherqualia/getRelations')}}", function(data) {
                console.log(data);
                let dataObject = JSON.parse(data);
                grapher.convert(dataObject);
                grapher.render();
            }, {idEntity: idEntity});
        }

        $('#lu').textbox({
            buttonIcon: 'icon-search',
            iconAlign:'right',
            prompt: 'Search',
            onClickButton: function() {
                $('#luTree').tree({queryParams: {lu: $('#lu').textbox('getValue')}});
            }
        });
        $('#lu').textbox('textbox').bind('keydown', function(e){
            if (e.keyCode == 13){
                $('#luTree').tree({queryParams: {lu: $('#lu').textbox('getValue')}});
            }
        });

        $('#grapherPanel').panel({
            height:'100%',
            width:'100%',
        });

        $('#btnReload').linkbutton({
            iconCls: 'icon-reload',
            plain: true,
            onClick: function() {
                if (grapher.currentEntity) {
                    grapher.show(grapher.currentEntity);
                }
            }
        });
        
        $('#luTree').tree({
            url: "{{$manager->getURL('grapher/qualia/luTree')}}",
                onSelect: function (node) {
                if (node.id.charAt(0) == 'l') {
                    grapher.show(node.id.substr(1));
                }
            }
        });

    });
</script>

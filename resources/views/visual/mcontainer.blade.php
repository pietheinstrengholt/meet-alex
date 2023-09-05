<!-- /resources/views/visual/mcontainer.blade.php -->
<div class="col-md-12 col-xs-12">
    <div id="MContainer">

        <div class="modal fade" id="inputDialog" role="dialog">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header header-xs">
                        <button type="button" class="close cancelDialog" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title" id='inputDialog_title'></h4>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="dataField">
                        <input type="text" placeholder="Enter name..." id="nameField">
                        <textarea id="descriptionField" placeholder="Enter description..."></textarea>
                    </div>
                    <div class="modal-footer header-xs">
                        <button type="button" id='btn_newTerm' class="btn btn-info btn-xs" data-dismiss="modal">Create</button>
                        <button type="button" id='btn_deleteTerm' class="btn btn-danger btn-xs" data-dismiss="modal">Delete</button>
                        <button type="button" class="btn btn-xs cancelDialog" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>


        <div id="Mmenu">
            <nav class="navbar navbar-default">
                <div class="container-fluid">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					 </button>
                    </div>
                    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                        <ul class="nav navbar-nav">
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Terms <span class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    <li> <a href="#" id="unlockAll" title="Unfix all elements from their position">Unfix all</a></li>
                                    <li> <a href="#" id="lockAll" title="Fix all elements to their position">Fix all</a></li>
                                    <li role="separator" class="divider"></li>
                                    <li> <a href="#" id="autoLockOn" title="Auto fix elements after moving them">Autofix On</a></li>
                                    <li> <a href="#" id="autoLockOff" title="Do not fix elements after moving them">Autofix Off</a></li>
                                    <li role="separator" class="divider"></li>
                                    <li> <a href="#" id="showLockOn" title="Show a pin when the element is fixed to a position">Show pin On</a></li>
                                    <li> <a href="#" id="showLockOff" title="Do not show a pin when the element is fixed">Show pin Off</a></li>
                                </ul>
                            </li>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Relations <span class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    <li> <a href="#" id="relationShowAll" title="Show all relations">All relations</a></li>
                                    <li> <a href="#" id="relationShowHierarchy" title="Show only the hierarchical relations">Hierarchy only</a></li>
                                    <li role="separator" class="divider"></li>
                                    <li> <a href="#" id="clusterIn" title="Cluster relation at incoming side">Cluster Incoming</a></li>
                                    <li> <a href="#" id="clusterOut" title="Cluster relation at outgoing side">Cluster Outgoing</a></li>
                                    <li> <a href="#" id="clusterNone" title="Show each relation separate">No clustering</a></li>
                                </ul>
                            </li>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Collection <span class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    <li> <a href="#" id="saveCollectionLayout" title="Save this layout">Save layout</a></li>
                                    <li> <a href="#" id="loadCollectionLayout" title="Load the layout">Load layout</a></li>
                                    <li> <a href="#" id="deleteCollectionLayout" title="Delete the layout">Delete layout</a></li>
                                </ul>
                            </li>
                            <li> <a href="#" id="viewCollection" title="Currently in Edit mode. Select to set to View mode">View mode</a></li>
                            <li> <a href="#" id="editCollection" title="Currently in View mode. Select to set to Edit mode">Edit mode</a></li>
                        </ul>
                        <ul class="nav navbar-nav navbar-right">
                            <input id="zoomSlide" data-slider-id='zoomSlider' type="text" data-slider-min="0.1" data-slider-max="2" data-slider-step="0.1" data-slider-value="1" />
                        </ul>
                    </div>
                    <!-- /.navbar-collapse -->
                </div>
                <!-- /.container-fluid -->
            </nav>
        </div>
        <div id="Mgraph">
            <img style="top:40%;" id="alexlogo" height="42" width="42" src={{ URL::asset( "img/spinner.gif") }}></img>
        </div>
    </div>
</div>
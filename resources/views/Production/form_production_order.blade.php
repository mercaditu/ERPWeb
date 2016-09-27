@extends('../../master')
@section('title', 'Order Trabajo | CognitivoERP')
@section('Title', 'Orden Trabajo')

@section('css')
	 <link href="{{ url() }}/assets/global/plugins/jstree/dist/themes/default/style.min.css" rel="stylesheet" type="text/css" />
@stop

@section('content')

<div class="row">
    <div class="col-md-4">
<div class="portlet light ">
    <div class="portlet-title">

        <div class="actions">
          


        </div>

         <div class="portlet-body">
        <div id="tree_1" class="tree-demo">
            <ul>
                <li id="name_parent"> 
                    <ul>
                        <li data-jstree='{ "selected" : true }'>
                            <a href="javascript:;" id="name_contact">  </a>
                        </li>
                      
                    </ul>
                </li>
               
            </ul>
        </div>
    </div>
    </div> 
    </div>   

    <div class="portlet light portlet-fit ">
        <div class="portlet-title">
            <div class="caption">
                <i class=" icon-layers font-green"></i>
                <span class="caption-subject font-green bold uppercase">Geolocation</span>
            </div>
            <div class="actions">
              
            </div>
        </div>
        <div class="portlet-body">
         <div class="form-group">                     
                       
                         
                {!! Form::textarea('address', null, ['class'=>'form-control', 'placeholder'=>'Address Contact','rows'=>'3','id'=>'address_contact']) !!}
                        
                    </div>
            <div class="label label-danger visible-ie8"> Not supported in Internet Explorer 8 </div>
            <div id="gmap_geo" class="gmaps"> </div>
        </div>
    </div>          
 </div>
 <div class="col-md-8">
<div class="portlet light ">            

         <div class="portlet-body form">
         @if (isset($production_order))
             {{--  <form class="form-horizontal" role="form" method="put" action="{{route('project_template.update',$template) }}"> --}}
             {!! Form::model($production_order,['route' => ['production_order.update',$production_order], 'method'=>'put','class'=> 'form-horizontal']) !!}
         @else
              <form class="form-horizontal" role="form" method="post" action="{{route('production_order.store') }}">
         @endif


           {!! csrf_field() !!}

        
             <div class="form-body">
                 <div class="form-group">
                            <label class="control-label col-md-3">Número OT</label>
                            <div class="col-md-9">
                                <p class="form-control-static">  </p>
                            </div>
                        </div>

                         <div class="form-group">
                            <label class="col-md-3 control-label">
                                Contacto
                            </label>
                            <div class="col-md-9">
                                <div class="input-icon">
                                    <i class="fa fa-bell-o">
                                    </i>
                                    <input class="form-control" placeholder="Left icon" type="text" id="contact" name="contact" />
                                    <input type="hidden" name="id_contact" id="id_contact" value="">
                                </div>
                            </div>
                        </div>

                             <div class="form-group">
                        <label class="col-md-3 control-label">
                            Reclamo
                        </label>
                        <div class="col-md-9">
                          {{--   <input class="form-control" placeholder="Enter text" type="text" name="name"  />     --}}
                            {!! Form::text('reclamo', null, ['class'=>'form-control', 'placeholder'=>'Full Name']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label">
                           Area
                        </label>
                        <div class="col-md-9">
                         {!!  Form::select('type_item',$project_tags,null,['class'=> 'form-control' ,'id'=>'type_item']) !!}

                        </div>
                    </div>

                                    <div class="form-group">
                                        <label class="control-label col-md-3">Rango de Fecha</label>
                                        <div class="col-md-9">
                                            <div class="input-group defaultrange" >
                                                <input type="text" class="form-control" required="" readonly=""
                                                       name="rango_fecha">
                                                <span class="input-group-btn">
                                                    <button class="btn default date-range-toggle" type="button">
                                                        <i class="fa fa-calendar"></i>
                                                    </button>
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                
                          <div class="form-group">
                        <label class="col-md-3 control-label">
                           Linea de Producción
                        </label>
                        <div class="col-md-9">
                         {!!  Form::select('id_production_line',$production_line,null,['class'=> 'form-control' ,'id'=>'id_production_line']) !!}

                        </div>
                    </div>

                          <div class="form-group">
                        <label class="col-md-3 control-label">
                           Tipo Trabajo
                        </label>
                        <div class="col-md-9">

                         <div class="input-group">
                            {!!  Form::select('id_project',$templates,null,['class'=> 'form-control' ,'id'=>'id_project']) !!}
                            <span class="input-group-addon">
                            <a  data-target="#load_template" data-toggle="modal" id="link_template">
                                 <i class="fa fa-user"></i>
                            </a>
                               
                            </span>
                        </div>                      

                        </div>   

                        <input type="hidden" name="name" id="name_production_order">             
                        </div>
             </div>
         
        
                <div class="form-actions">
                    <div class="row">
                        <div class="col-md-offset-3 col-md-9">
                            <button class="btn green" type="submit">
                                Submit
                            </button>
                            <button class="btn default" type="button">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
        

          <input type="hidden" name="tree_save" id="tree_save">

        

            {!! Form::close() !!}

    </div>



</div>
</div>



  

<!--DOC: Aplly "modal-cached" class after "modal" class to enable ajax content caching-->
<div class="modal fade" id="load_template" role="basic" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body" id="modal_template">
                  <div class="actions pull-right">
          {{--   <a class="btn btn-circle btn-icon-only btn-default" href="javascript:;"  id="add_task_production_order">
                <i class="icon-cloud-upload"></i>
            </a> --}}
            <a class="btn btn-circle btn-icon-only btn-default" href="javascript:;" id="update_task_production_order" data-token="{{ csrf_token() }}">
                <i class="icon-wrench"></i>
            </a>
            <a class="btn btn-circle btn-icon-only btn-default" href="javascript:;" id="remove_task" data-token="{{ csrf_token() }}">
                <i class="icon-trash"></i>
            </a>


        </div>
               <div id='jstree' class='tree-demo' ></div>
              
                
            </div>
        </div>
    </div>
</div>


 

@stop



@section('scripts')
	<!-- BEGIN PAGE LEVEL PLUGINS -->
        <script src="{{ url() }}/assets/global/plugins/jstree/dist/jstree.min.js" type="text/javascript"></script>       
        <!-- END PAGE LEVEL PLUGINS -->

        

 {{--        <script async defer
      src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAJ_Pf9r3W4LqU71Br79LK8pFDD6nrfXRU&callback=initMap">
    </script> --}}
       
         <script src="http://maps.google.com/maps/api/js?key=AIzaSyAJ_Pf9r3W4LqU71Br79LK8pFDD6nrfXRU" type="text/javascript"></script>
        <script src="../assets/global/plugins/gmaps/gmaps.min.js" type="text/javascript"></script>

    
        
           <script src="{{ url() }}/assets/pages/scripts/tree-view-template.js" type="text/javascript"></script>
      


       <script type="text/javascript">
		
		$('#link_template').click(function(){
              //console.log($('#jstree1').jstree())
              var id_project_id_project_template = $('#id_project option:selected').val()
              var id_project_template= id_project_id_project_template.split("-")[1]        
                     
           load_tree_project_order(id_project_template)
   
		})


	</script>

@stop





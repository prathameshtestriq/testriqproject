@extends('layout.index')
@section('title', 'Farmer Details')

@section('content')

<section>
    <div class="col-md-1"></div>
    <div class="content-body">
        <section id="multiple-column-form">
            <div class="row justify-content-center">
                <div class="col-sm-12">
                    <div class="card m-1" style="border-radius:15px">
                        <div class="card-body custom_active" style="margin-bottom: -10px">
                            <ul id="tab_outer" class="row dashboard-tab"></ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div id="form_outer"></div>
    
    <div id="myModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add Cycle</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body" id="add_cycle_html">
                    <p>Form loading...</p>
                </div>
                <!-- <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div> -->
            </div>

        </div>
    </div>
<section>


<script src="http://code.jquery.com/jquery-1.11.3.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.3.0/jquery.form.min.js" integrity="sha384-qlmct0AOBiA2VPZkMY3+2WqkHtIQ9lSdAsAn5RUJD/3vA5MKDgSGcdmIv4ycVxyn" crossorigin="anonymous"></script>
<script>
    const country_id = {{$country_id}};
    const farmer_id = {{$farmer_id}};
    const program_id = {{$program_id}};

    var token = 'YVFvUW50cUlwaXRmMzhJN3ptTlpEZndFbDBOMEhCRXE6bHFwVzJmNUdHVjd2bGY0UA==';
    var sAdminUrl = 'http://192.168.1.107:8000/'
    var input = {};

    var farmerData = {};
    var selectedFarmerData = {};
    var currentTabsForms = []
    var tab = "{{isset($_GET['tab']) ? $_GET['tab'] : ''}}"
  
    function get_farmer_data(){
        var input = {
            "country_id": country_id,
            "program_id": program_id,
            "farmer_ids": [farmer_id]
        };

        $.ajax({
            type:"POST",
            url:sAdminUrl+"api/form_get_tab_forms_data",
            data: JSON.stringify(input),
            contentType: "application/json; charset=utf-8",
            headers: { "Authorization": 'Bearer ' + token },
            complete:function(response){
                var obj = jQuery.parseJSON( response.responseText );
                if(obj.error==0){
                    farmerData = obj.data
                    html = '';
                    $.each( obj.data.farmers, function( key, singleObject ) {
                        if(farmer_id==singleObject.farmer_id){
                            selectedFarmerData = singleObject
                        }
                    });
                    $.each( obj.data.tabs, function( key, singleObject ) {
                        html += '<li class="col-xs-1 ml-1"  style="list-style: none;"><a style="font-size:15px" class="btn tabs-all" id="tab_'+singleObject.tab_id+'" href="javascript:void(0)" onclick="get_forms('+key+')">'+singleObject.tab_title+'</a></li>'
                    });
                    
                    $("#tab_outer").html(html);

                    if(tab!==''){
                        get_forms(tab)
                    }
                }
            }  
        });
    }
    get_farmer_data()

    function get_forms(key){
        tab = key;
        $('.tabs-all').removeClass('active');
        $('#tab_'+farmerData.tabs[key].tab_id).addClass('active')
        currentTabsForms = farmerData.tabs[key].forms_data
        render_form(farmerData.tabs[key].forms_data)
    }

    function render_form(forms_data){
        let formHtml = ''
        for(let i=0; i<forms_data.length; i++){
            formHtml += '<div class="content-body">'
            formHtml += '<section id="multiple-column-form">'
            formHtml += '<div class="row justify-content-center">'
            formHtml += '<div class="col-sm-12">'
            formHtml += '<div class="card m-1" style="border-radius:15px">'
            formHtml += '<div class="card-body custom_active" style="margin-bottom: -10px">'
            formHtml += '<h4>'+ forms_data[i].form_title +'</h4>'
            if((forms_data[i].form_type==0)){
                let questions = get_questions_html(forms_data[i])
                formHtml += questions
            }else{
                questionsData = get_questions_data_html(forms_data[i])
                formHtml += questionsData
            }
            formHtml += '</div>'
            formHtml += '</div>'
            formHtml += '</div>'
            formHtml += '</div>'
            formHtml += '</section>'
            formHtml += '</div>'
        }
        $("#form_outer").html(formHtml);
    }

    function get_questions_data_html(forms_data){
        // console.log(forms_data)
        // console.log(selectedFarmerData)

        let farmerActivites = []
        for(let i=0; i<selectedFarmerData.forms.length; i++){
            if(selectedFarmerData.forms[i].form_id==forms_data.form_id){
                farmerActivites = selectedFarmerData.forms[i].activities
            }
        }

        // console.log(farmerActivites)

        let questions = forms_data.questions
        let html = '<div class="mb-1 text-right"><input type="button" value="Add Cycle" class="btn btn-primary waves-effect waves-float waves-light" onclick="add_cycle('+forms_data.form_id+','+(farmerActivites.length+1)+')"></div>'
        html += '<div class="row">'
        html += '<div class="col-md-12 table-responsive">'
        html += '<table id="form_table_'+forms_data.form_id+'" class="table table-striped table-bordered">'
        html += '<tr>'
        for(let i=0; i<questions.length; i++){
            html += '<th>'+questions[i].question_text+'</th>'
        }
        html += '<th>Action</th>'
        html += '</tr>'

        for(let j=0; j<farmerActivites.length; j++){
            html += '<tr>'
            for(let i=0; i<questions.length; i++){
                html += '<td>'+farmerActivites[j][questions[i].question_id]+'</td>'
            }
            html += '<td><a href="javascript:void(0)" onclick="add_cycle('+forms_data.form_id+','+farmerActivites[j].activity_number+')" title="Edit"><i class="fa fa-edit"></i></a></td>'
            html += '</tr>'
        }

        html += '</table>'
        html += '</div>'
        html += '</div>'
        return html;
    }

    function add_cycle(form_id, activity_number=0){
        $('#myModal').modal('show')
        let currentFormsData = {}
        
        for(let i=0; i<currentTabsForms.length; i++){
            if(form_id==currentTabsForms[i].form_id){
                currentFormsData = currentTabsForms[i]
                break;
            }
        }
        
        $('#add_cycle_html').html(get_questions_html(currentFormsData, activity_number))
    }

    var a_subquestions = [];
    var a_form_fields_data = [];

    function get_questions_html(forms_data, activity_number = 0){
        let farmerActivites = []
        for(let i=0; i<selectedFarmerData.forms.length; i++){
            if(selectedFarmerData.forms[i].form_id==forms_data.form_id){
                farmerActivites = selectedFarmerData.forms[i].activities
            }
        }

        let activityIndex = 0
        for(let j=0; j<farmerActivites.length; j++){
            if(activity_number==farmerActivites[j].activity_number){
                activityIndex = j
            } 
        }
        
        let questions = forms_data.questions
        
        html = '<div class="row">'
        html += '<div class="col-md-12">'
        html += '<form id="form_'+forms_data.form_id+'">'
        $.each(questions , function( key, question_obj ){
            let answer = farmerActivites.length > 0 ? farmerActivites[activityIndex][question_obj.question_id] : ''
            
            if(forms_data.form_type==1 && activity_number > farmerActivites.length){
                answer = ''
            }

            field = get_input(question_obj.type, question_obj, answer);
            if(field != ''){
                display = '';
                if(question_obj.parent_questions !== 0){
                    display = 'display:none';
                }
                html += '<div class="row mb-1 parent_'+parseInt(question_obj.parent_questions)+'" id="question_head_'+parseInt(question_obj.id)+'" style="'+display+'" ><div class="col-md-5">'+question_obj.question_text+'</div><div class="col-md-7">'+field+'<span class="error" id="error_'+question_obj.id+'"></span></div></div>';
            }
        })
        html += '<div class="row"><div class="col-md-12"><input type="button" onclick="save_form('+forms_data.form_id+','+forms_data.form_type+','+activity_number+')" value="Save" class="btn btn-primary waves-effect waves-float waves-light"></div></div>'
        html += '</form>'
        html += '</div>'
        html += '</div>'
        
        // $(document).on('click', '#savebutton_'+forms_data.form_id, function() {
            
        // });
        return html;
    }

    function save_form(form_id, form_type, activity_number){
        $('.error').html('')

        $('#form_'+form_id).ajaxSubmit({
            headers: { "Authorization": 'Bearer ' + token }, 
            url: sAdminUrl+'api/form_save_form_data', 
            type: 'post',
            data:{'country_id':country_id, 'form_id':form_id, 'farmer_id':farmer_id, 'form_type':form_type, 'activity_number':activity_number},
            success: function(){
                let url = window.location.pathname;
                url += '?tab='+tab
                window.location.href = url;
            },
            error:function(e){
                let message = e.responseJSON.message
                for (let key in message) {
                    $('#error_'+key).html(message[key])
                }
            }
        });
    }

    function get_input(question_type, question_obj, answer){
        if(question_type == 'auto_next'){
            return '<lable>'+question_obj.question_text+'</lable>'
        }else if(question_type == 'hide_on_form'){
            return '';
        }else if(question_type == 'input'){
            return '<input required class="form-select form-control" type="text" id=question_'+question_obj.id+' name="answer['+question_obj.id+']" value="'+answer+'">'
        }else if(question_type == 'selection'){
            if( 'has_question_onchange' in question_obj){
                $(document).on('change', '#question_'+question_obj.id, function(){
                    $('.parent_'+question_obj.id).hide();
                    a_question_ids = this.options[this.selectedIndex].getAttribute('childquestionids').split(',')
                    $.each(a_question_ids,function(key, options){
                        $('#question_head_'+options).show();
                    });
                });
            }
            dropdown = '<select class="form-select form-control" id=question_'+question_obj.id+' name="answer['+question_obj.id+']">'
            $.each(question_obj.options , function( key, options ){
                dropdown += '<option childquestionids="'+options.child_questions+'" value="'+options.key+'" '+(answer==options.key ? 'selected' : '')+' >'+options.value+'</option>'
                
                if(answer==options.key && options.child_questions!=''){
                    setTimeout(()=>{
                        $('.parent_'+question_obj.id).hide();
                        a_question_ids = options.child_questions.split(',')
                        $.each(a_question_ids,function(key, options){
                            $('#question_head_'+options).show();
                        });
                    }, 1000);
                }
            })
            dropdown += '</select>'
            return dropdown
        }else if(question_type == 'radio'){
            radio = '<div>';

            if( 'has_question_onchange' in question_obj){
                $(document).on('change', 'input[type=radio][name="answer['+question_obj.id+']"]', function(){
                    $('.parent_'+question_obj.id).hide();
                    a_question_ids = this.getAttribute('childquestionids').split(',')
                    $.each(a_question_ids,function(key, options){
                        $('#question_head_'+options).show();
                    });
                });
            }
            
            $.each(question_obj.options , function( key, options ){
                radio += '<input class="form-radio" childquestionids="'+options.child_questions+'" type="radio" value="'+options.key+'" name="answer['+question_obj.id+']" '+(answer==options.key ? 'checked' : '')+' />&nbsp;'+options.value+'&nbsp;&nbsp;&nbsp;'

                if(answer==options.key && options.child_questions!=''){
                    setTimeout(()=>{
                        $('.parent_'+question_obj.id).hide();
                        a_question_ids = options.child_questions.split(',')
                        $.each(a_question_ids,function(key, options){
                            $('#question_head_'+options).show();
                        });
                    }, 1000);
                }
            })
                radio += '</div>'
            return radio
        }else if(question_type == 'checkbox'){
            check = '<div>';
            
            if( 'has_question_onchange' in question_obj){
                $(document).on('change', 'input[type=checkbox][name="answer['+question_obj.id+']"]', function(){
                    $('.parent_'+question_obj.id).hide();
                    a_question_ids = this.getAttribute('childquestionids').split(',')
                    $.each(a_question_ids,function(key, options){
                        $('#question_head_'+options).show();
                    });
                });
            }

            $.each(question_obj.options , function( key, options ){
                check += '<div class="form-check">'
                check += '<input class="form-check-input" childquestionids="'+options.child_questions+'" type="checkbox" value="'+options.key+'" name="answer['+question_obj.id+']" id="answer_'+question_obj.id+'" '+(answer.includes(options.key) ? 'checked' : '')+'>'
                check += '<label class="form-check-label" for="answer_'+question_obj.id+'">'
                check += options.value
                check += '</label>'
                check += '</div>'
               
                if(answer==options.key && options.child_questions!=''){
                    setTimeout(()=>{
                        $('.parent_'+question_obj.id).hide();
                        a_question_ids = options.child_questions.split(',')
                        $.each(a_question_ids,function(key, options){
                            $('#question_head_'+options).show();
                        });
                    }, 1000);
                }
            })

            check += '</div>'
            return check
        }else{
            return answer;
        }
    }
      
    // var token = 'YVFvUW50cUlwaXRmMzhJN3ptTlpEZndFbDBOMEhCRXE6bHFwVzJmNUdHVjd2bGY0UA==';
    // var sAdminUrl = 'http://meladmin.swtprime.com/'
    // var input = {};

    // var a_subquestions = [];
    // var a_form_fields_data = [];

    // get_tab_data();
    
    // function get_tab_data(){
    //     var input = {"program_id":program_id,'farmer_id':farmer_id};
    //     $.ajax({
    //         type:"POST",
    //         url:sAdminUrl+"api/form_get_tabs",
    //         data: JSON.stringify(input),
    //         contentType: "application/json; charset=utf-8",
    //         headers: { "Authorization": 'Bearer ' + token },
    //         complete:function(response){
    //             var obj = jQuery.parseJSON( response.responseText );
    //             html = '';
                
    //             $.each( obj.data, function( key, singleObject ) {
    //                 // html += "<input type='button' class='tabs' id="+singleObject.tab_id+" value='"+singleObject.tab_title+"'>"
    //                 html += '<li class="col-xs-1 ml-1"  style="list-style: none;"><a style="font-size:15px" class="btn tabs-all" id="'+singleObject.tab_id+'" href="javascript:void(0)">'+singleObject.tab_title+'</a></li>'
                    
    //             });
                
    //             $("#tab_outer").html(html);
    //             $(document).on('click', '.tabs-all', function() {
    //                 $('.tabs-all').removeClass('active');
    //                 $('#'+this.id).addClass('active')
    //                 get_tab_form_data(this.id)
    //             })
    //         }  
    //     });
    // }

    // function get_tab_form_data(tab_id){
    //     input = {"program_id":program_id,'tab_id':tab_id,'farmer_id':farmer_id,'country_id':country_id};
    //     $.ajax({
    //         type:"POST",
    //         url:sAdminUrl+"api/form_get_tab_forms_data",
    //         data: JSON.stringify(input),
    //         contentType: "application/json; charset=utf-8",
    //         //data:"select="+select+'&filter='+filter+'&chartData='+chartData,
    //         headers: { "Authorization": 'Bearer ' + token },
    //         complete:function(response){
    //             var obj = jQuery.parseJSON( response.responseText );
    //             show_forms(obj.data)
    //         }  
    //     });
    // }

    // function show_forms(form_data){
    //     html = ''
    //     $.each( form_data, function( key, form_obj ) { 
    //         //console.log(form_obj)
    //         html += '<div class="form-class"><h1>'+form_obj.form_title+'</h1><div class="form_div"><button id="program_form_id'+form_obj.program_form_id+'">Add</button>'
            
    //         html += '</div></div>'
    //         $(document).on('click', '#program_form_id'+form_obj.program_form_id, function() { 
    //             get_form_fields(this.id);
    //         });
    //     });
    //     $('#form_outer').html(html)
    // }

    // function get_form_fields(form_id){
    //     input.program_form_id = form_id
    //     $.ajax({
    //         type:"POST",
    //         url:sAdminUrl+"api/form_get_form_fields",
    //         data: JSON.stringify(input),
    //         contentType: "application/json; charset=utf-8",
    //         //data:"select="+select+'&filter='+filter+'&chartData='+chartData,
    //         headers: { "Authorization": 'Bearer ' + token },
    //         complete:function(response){
    //             var obj = jQuery.parseJSON( response.responseText );
    //             a_form_fields_data = obj.data;
    //             console.log(obj)
    //             // draw_form_new(obj)
    //         }  
    //     });
    // }
</script>
@endsection
          <div class="panel panel-info">

            <div class="panel-body">
              <div class="row">
                <div class="col-md-5 col-lg-5 ">

                  <div class="img-circle" alt="member Image" align="center">
                  {!! $errors->first('photo','<label class="has-error">:message</label>') !!} 
                  <div class="track-info-art">
                     
                      <div class="button-normal upload-btn" id="upload-photo-btn">
                              @include('files.show',['filename'=>$member->photo])
                      </div>
                          
                          <span id="cover-photo" class="upload-btn-text">
                             {{ trans('general.upload_photo') }}
                          </span>
                          
                          <span id="cover-photo-sel" class="upload-btn-text" style="display: none;color:green;font-weight:bold;">
                          {{ trans('general.photo_selected') }}
                          </span> 
                          <input type="file" name="photo" id="upload-photo" accept="image/*">

                      </div>
                  </div>

             <div class="col-xs-12 col-sm-12 "> <br>
                  <dl>
                    <dt>{{ trans('member.adhersion_number') }} <em style="font-size:12px;font-weight:100"></em>
                      {!! $errors->first('registration_number','<em class="has-error">(:message)</em>') !!}
                    </dt>
                    <dd class=" {{ ($errors->has('registration_number')) ? 'has-error' : '' }}">
                     {!! Form::text('registration_number', $member->adhersion_id, ['class'=>'form-control border border-gray-300','placeholder'=>trans('member.adhersion_number'),'disabled']) !!}
                  </dd>

                  <dt>{{ trans('member.province') }}
                     {!! $errors->first('province','<em class="has-error">(:message)</em>') !!}
                    </dt>
                    <dd class=" {{ ($errors->has('province')) ? 'has-error' : '' }}">                      
                    {!! Form::select('province',['Est'=>'Est','Kigali'=>'Kigali','West'=>'West','South'=>'South','North'=>'North'],$member->province, ['class' => 'form-control']) !!}
                       
                    </dd>
                    <dt>{{ trans('member.district') }} <em style="font-size:12px;font-weight:100"></em>
                      {!! $errors->first('district','<em class="has-error">(:message)</em>') !!}
                    </dt>
                <!---    <dd class=" {{ ($errors->has('district')) ? 'has-error' : '' }}">
                     {!! Form::text('district', $member->district, ['class'=>'form-control border border-gray-300','placeholder'=>trans('member.district')]) !!}
                  </dd> -->
                    <td class=" {{ ($errors->has('district')) ? 'has-error' : '' }}">
                           {!! Form::select('district',['Rwamagana'=>'Rwamagana','Bugesera'=>'Bugesera','Gatsibo'=>'Gatsibo','Kayonza'=>'Kayonza','Kirehe'=>'Kirehe','Ngoma'=>'Ngoma','Nyagatare'=>'Nyagatare','Gasabo'=>'Gasabo','Kicukiro'=>'Kicukiro','Nyarugenge'=>'Nyarugenge','Burera'=>'Burera','Gakenke'=>'Gakenke','Gicumbi'=>'Gicumbi','Musanze'=>'Musanze','Rulindo'=>'Rulindo','Gisagara'=>'Gisagara','Huye'=>'Huye','Kamonyi'=>'Kamonyi','Muhanga'=>'Muhanga','Nyamagabe'=>'Nyamagabe','Nyanza'=>'Nyanza','Nyaruguru'=>'Nyaruguru','Ruhango'=>'Ruhango','Karongi'=>'Karongi','Ngororero'=>'Ngororero','Nyabihu'=>'Nyabihu','Nyamasheke'=>'Nyamasheke','Rubavu'=>'Rubavu','Rusizi'=>'Rusizi','Rutsiro'=>'Rutsiro'],$member->district, ['class' => 'form-control']) !!}
                        </td>
                    
                    <dt>{{ trans('member.institution') }}
                     {!! $errors->first('institution','<em class="has-error">(:message)</em>') !!}
                    </dt>
                    <dd class=" {{ ($errors->has('institution')) ? 'has-error' : '' }}">
                      {!! Form::select('institution_id', $institutions, $member->institution_id,['class'=>'form-control border border-gray-300']) !!}
                    </dd>

                    <dt>{{ ucfirst(trans('member.status')) }}
                     {!! $errors->first('status','<em class="has-error">(:message)</em>') !!}
                    </dt>

                    <dd class=" {{ ($errors->has('status')) ? 'has-error' : '' }}">
                      {!! Form::select('status', ['actif' => trans('member.actif'),'inactif'=> trans('member.inactif'),'left'=> trans('member.left')], $member->status,
                      ['class'=>'form-control border border-gray-300','id'=>'member-status']) !!}
                    </dd>

                    <dt>{{ trans('member.service') }}
                     {!! $errors->first('service','<em class="has-error">(:message)</em>') !!}
                    </dt>
                    <dd class=" {{ ($errors->has('service')) ? 'has-error' : '' }}">
                        {!! Form::text('service', $member->service, ['class'=>'form-control border border-gray-300','placeholder'=>trans('member.service') ]) !!}
                    </dd>
                    <dt>{{ trans('member.monthly_fee') }}:
                         {!! $errors->first('monthly_fee','<em class="has-error">(:message)</em>') !!}
                     </dt>
                    <dd  class=" {{ ($errors->has('monthly_fee')) ? 'has-error' : '' }}">

                          {!! Form::text('monthly_fee', $member->monthly_fee,
                                         ['class'=>'form-control border border-gray-300',
                                          'placeholder'=> trans('member.monthly_fee')]
                                        )
                          !!}
                       </dd>

                    <dt>{{ trans('member.employee_id') }}
                     {!! $errors->first('employee_id','<em class="has-error">(:message)</em>') !!}
                    </dt>
                    <dd class=" {{ ($errors->has('employee_id')) ? 'has-error' : '' }}">
                        {!! Form::text('employee_id', $member->employee_id, ['class'=>'form-control border border-gray-300',
                       'placeholder'=>trans('member.employee_id') ]) !!}
                    </dd>
                   <dt>{{ trans('member.termination_date') }}
                     {!! $errors->first('termination_date','<em class="has-error">(:message)</em>') !!}
                    </dt>
                    <dd class=" {{ ($errors->has('termination_date')) ? 'has-error' : '' }}">
                        {!! Form::text('termination_date', $member->termination_date, ['class'=> 'form-control',
                        'data-toggle'=>"datepicker",'placeholder'=>trans('member.termination_date') ]) !!}
                    </dd>
                  </dl>
                </div>

                </div>


                <div class=" col-md-7 col-lg-7 ">
                                    <div class="rounded-lg md:w-25" alt="member Image" align="center">
                    {!! $errors->first('signature','<label class="has-error">:message</label>') !!} 
                  <div class="track-info-art" >
                      <div class="upload-btn" id="upload-signature-btn">
                            @include('files.show',['filename'=>$member->signature])
                         </div>
                          <span id="cover-signature" class="upload-btn-text">
                            {{ trans('general.upload_signature') }}
                          </span>
                          <span id="cover-signature-sel" class="upload-btn-text" style="display: none;color:green;font-weight:bold;">{{ trans('general.signature_selected') }}</span>
                          <input type="file" name="signature" id="upload-signature" accept="image/*">
                          </div>
                      </div>
                 
                  <table class="table table-user-information">
                    <tbody>
                      <tr>
                        <th>{{ trans('member.first_name') }}:
                          {!! $errors->first('first_name','<em class="has-error">(:message)</em>') !!}
                        </th>
                        <td class=" {{ ($errors->has('first_name')) ? 'has-error' : '' }}" style="border-top: none;">
                          {!! Form::text('first_name',$member->first_name,['class'=>'form-control border border-gray-300','placeholder'=>trans('member.first_name') ]) !!}
                           </td>
                      </tr>

                       <tr>
                        <th>{{ trans('member.last_name') }}:
                          {!! $errors->first('last_name','<em class="has-error">(:message)</em>') !!}
                        </th>
                        <td class=" {{ ($errors->has('last_name')) ? 'has-error' : '' }}" style="border-top: none;">
                          {!! Form::text('last_name',$member->last_name, ['class'=>'form-control border border-gray-300','placeholder'=>trans('member.last_name') ]) !!}
                           </td>
                      </tr>

                      <tr>
                        <th>{{ trans('member.date_of_birth') }}:
                        {!! $errors->first('date_of_birth','<em class="has-error">(:message)</em>') !!}
                        </th>

                        <td class=" {{ ($errors->has('date_of_birth')) ? 'has-error' : '' }}">
                          {!! Form::text('date_of_birth',date('Y-m-d',strtotime($member->date_of_birth)),['class'=>'form-control',
                            'id'=>'date2',
                            'data-toggle'=>"datepicker"]) !!}</td>
                      </tr>

                      <tr>
                        <tr>
                        <th>{{ trans('member.sex') }}:
                      {!! $errors->first('sex','<em class="has-error">(:message)</em>') !!}
                        </th>
                        <td class=" {{ ($errors->has('sex')) ? 'has-error' : '' }}">
                           {!! Form::select('sex',['Male'=> trans('member.male'),'Female'=> trans('member.female')],$member->sex, ['class' => 'form-control']) !!}
                        </td>
                      </tr>

                       <tr>
                        <th>{{ trans('member.nid') }}:
                        {!! $errors->first('member_nid','<em class="has-error">(:message)</em>') !!}
                        </th>
                        <td class=" {{ ($errors->has('member_nid')) ? 'has-error' : '' }}">
                        {!! Form::text('member_nid', $member->member_nid, ['class'=>'form-control border border-gray-300','placeholder'=> trans('member.nid') ]) !!}
                        </td>
                      </tr>
                        <tr>
                        <th>{{ trans('member.nationality') }}:
                          {!! $errors->first('nationality','<em class="has-error">(:message)</em>') !!}
                        </th>
                        <td class=" {{ ($errors->has('nationality')) ? 'has-error' : '' }}">
                          {!! Form::select('nationality',['Rwandese'=> trans('member.rwandese'),'Other'=> trans('member.other')], $member->nationality, ['class'=>'form-control border border-gray-300','placeholder'=>trans('member.nationality')]) !!}</td>
                      </tr>
                      <tr>
                        <th>{{ trans('member.email') }}:
                         {!! $errors->first('email','<em class="has-error">(:message)</em>') !!}
                        </th>
                        <td class=" {{ ($errors->has('email')) ? 'has-error' : '' }}">
                           {!! Form::input('email','email', $member->email, ['class'=>'form-control border border-gray-300 email','placeholder'=>trans('member.email')]) !!}
                        </td>
                      </tr>
                      <tr>
                        <th> {{ trans('member.telephone') }}:
                         {!! $errors->first('telephone','<em class="has-error">(:message)</em>') !!}
                        </th>
                        <td class=" {{ ($errors->has('telephone')) ? 'has-error' : '' }}">

                          {!! Form::text('telephone', $member->telephone, ['class'=>'form-control border border-gray-300','placeholder'=> trans('member.telephone')]) !!}
                        </td>
                      </tr>
                      
                        <tr>
                        <th>{{ trans('member.bank') }}:
                      {!! $errors->first('sex','<em class="has-error">(:message)</em>') !!}
                        </th>
                        <td class=" {{ ($errors->has('bank')) ? 'has-error' : '' }}">
                          {!! Form::select('bank_id', $banks, $member->bank_id,['class'=>'form-control border border-gray-300']) !!}
                        </td>
                      </tr>
                      <tr>
                        <th> {{ trans('member.bank_account') }}:
                         {!! $errors->first('bank_account','<em class="has-error">(:message)</em>') !!}
                        </th>
                          <td class=" {{ ($errors->has('bank_account')) ? 'has-error' : '' }}">
                          {!! Form::text('bank_account', $member->bank_account, ['class'=>'form-control border border-gray-300']) !!}
                        </td>
                      </tr>
                      
                    </tbody>
                  </table>
                 @if (!Sentry::getUser()->isNormalMember())
                  <a href="{!!route('members.index')!!}" class="btn btn-warning"><i class="fa fa-remove"></i> {{ trans('member.cancel') }}</a>
                  <button href="#" class="btn btn-success" id="save-member"><i class="fa fa-floppy-o"></i> {!! $button !!}</button>
                 @endif
                </div>
              </div>
            </div>
    </div>
<?php

namespace Vuravel\Form\Components;

use Vuravel\Form\Field;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MultiForm extends Field
{
    public $component = 'MultiForm';

    public $multiple = true;

    public $formClass;

    protected function vlInitialize($name)
    {
        parent::vlInitialize($name);
        $this->name = lcfirst(Str::camel($name));
    }

    public function prepareValueForFront($record)
    {
        if($this->value){
            //Only works for Eloquent relation, MultiForm cannot be an attribute currently
            $this->components = $this->value->map(function($item){
                $formClass = $this->formClass;
                return $formClass::find($item->getKey());
            })->all();
        }
    }

    public function mounted($form)
    {
        $rules = collect($this->components[0]->getValidationRules())->flatMap(function($v, $k){
            $k = $this->name.'.*.'.$k;
            return [$k => $v];
        })->all();

        $form->addValidationRules($rules);
    }



    protected function setRelationFromRequest($request, $record)
    {
        $this->value = collect($request->__get($this->name))->map(function($subrequest){
            
            $request = new Request($subrequest);
            
            $formClass = $this->formClass;
            $form = with(new $formClass(true))->bootFromRequest($request);

            if($form->recordKey){
                $form->updateRecordFromRequest($request);
                return null; //the work has been done
            }else{
                return $form->newModelInstanceFromRequest($request)->toArray(); //saved in next step BUT IT'S MISSING RELATIONSHIP SAVING STEP :(
            }
        })->filter();
    }

    /**
     * To retrieve one or multiple new forms from the backend.
     *
     * @param      string  $route    The route name or uri.
     * @param      array|null  $parameters   The route parameters (optional).
     * @param      array|null  $ajaxPayload  Additional custom data to add to the request (optional).
     *
     * @return     self    
     */
    public function formRoute($route, $parameters = null, $ajaxPayload = null)
    {
        $this->setRoute($route, $parameters);
        $this->setRouteMethod('POST');

        $this->data(['sessionTimeoutMessage' => __('sessionTimeoutMessage')]);

        if($ajaxPayload)
            $this->data(['ajaxPayload' => $ajaxPayload]);

        if(!$this->formClass)
            $this->formClass($this->getVuravelObjectFromRoute());

        return $this;
    }

    /**
     * Sets the fully qualified class name of the form that will be loaded from the Back-end or displayed multiple times when displaying relationships.
     *
     * @param      string  $formClass  The fully qualified form class. Ex: App\Forms\MyForm::class
     */
    public function formClass($formClass)
    {
        $this->formClass = $formClass;
        $this->components = [ new $formClass() ];

        return $this;
    }

}

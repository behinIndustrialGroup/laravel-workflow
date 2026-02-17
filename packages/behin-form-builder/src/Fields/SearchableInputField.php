<?php

namespace MyFormBuilder\Fields;

class SearchableInputField extends AbstractField
{
    public function render(): string
    {
        $id = $this->attributes['id'] ?? $this->name;
        $containerId = $id . '_searchable_container';
        $hiddenId = $id . '_value';
        $searchInputId = $id . '_search';
        $resultsId = $id . '_results';
        $endpoint = $this->attributes['endpoint'] ?? '';
        $minChars = isset($this->attributes['minChars']) ? (int)$this->attributes['minChars'] : 3;
        $limit = isset($this->attributes['limit']) && $this->attributes['limit'] !== ''
            ? (int)$this->attributes['limit']
            : null;
        $value = $this->attributes['value'] ?? '';
        $initialLabel = $this->attributes['initial_label'] ?? ($this->attributes['initialLabel'] ?? '');
        $placeholder = $this->attributes['placeholder'] ?? '';
        $readonly = $this->attributes['readonly'] ?? '';
        $required = $this->attributes['required'] ?? '';

        $label = trans('fields.' . $this->name);
        $requiredMark = $required === 'on' && $readonly !== 'on' ? ' <span class="text-danger">*</span>' : '';
        $readonlyAttribute = $readonly === 'on' ? 'readonly' : '';
        $placeholderAttribute = $placeholder ? 'placeholder="' . htmlspecialchars($placeholder, ENT_QUOTES) . '"' : '';
        $requiredAttribute = $required === 'on' ? 'required' : '';
        $initialLabelAttribute = htmlspecialchars($initialLabel, ENT_QUOTES);
        $endpointAttribute = htmlspecialchars($endpoint, ENT_QUOTES);
        $valueAttribute = htmlspecialchars((string)$value, ENT_QUOTES);

        $config = json_encode([
            'containerId' => $containerId,
            'hiddenId' => $hiddenId,
            'inputId' => $searchInputId,
            'resultsId' => $resultsId,
            'endpoint' => $endpoint,
            'minChars' => $minChars,
            'limit' => $limit,
            'initialLabel' => $initialLabel,
            'fieldName' => $this->name,
        ]);

        $s = '<div class="form-group position-relative" id="' . $containerId . '">';
        $s .= '<label for="' . $searchInputId . '">' . $label . $requiredMark . '</label>';
        $s .= '<input type="hidden" name="' . $this->name . '" id="' . $hiddenId . '" value="' . $valueAttribute . '" ' . $requiredAttribute . ' data-role="searchable-value">';
        $s .= '<input type="text" class="form-control" id="' . $searchInputId . '" autocomplete="off" data-role="searchable-input" ';
        $s .= 'data-endpoint="' . $endpointAttribute . '" data-min-chars="' . $minChars . '" data-limit="' . ($limit ?? '') . '" ';
        $s .= 'data-initial-label="' . $initialLabelAttribute . '" ' . $placeholderAttribute . ' ' . $readonlyAttribute . '> ';
        $s .= '<div class="list-group" id="' . $resultsId . '" style="position:absolute; width:100%; z-index:1050;"></div>';
        $s .= '</div>';

        $s .= '<script>(function(){';
        $s .= 'const config = ' . $config . ';';
        $s .= 'const container = document.getElementById(config.containerId);';
        $s .= 'if(!container){return;}';
        $s .= 'const searchInput = document.getElementById(config.inputId);';
        $s .= 'const hiddenInput = document.getElementById(config.hiddenId);';
        $s .= 'const resultsBox = document.getElementById(config.resultsId);';
        $s .= 'if(!searchInput || !hiddenInput || !resultsBox){return;}';

        $s .= 'window.formBuilderSearchableFields = window.formBuilderSearchableFields || {};';
        $s .= 'const state = { timer:null, controller:null, loading:false };';

        $s .= 'function clearResults(){resultsBox.innerHTML="";resultsBox.style.display="none";}';
        $s .= 'function showBox(){resultsBox.style.display="block";}';

        $s .= 'function showHint(){';
        $s .= 'clearResults();showBox();';
        $s .= 'const div=document.createElement("div");';
        $s .= 'div.className="list-group-item text-muted small";';
        $s .= 'div.textContent="حداقل " + config.minChars + " کاراکتر وارد کنید";';
        $s .= 'resultsBox.appendChild(div);';
        $s .= '}';

        $s .= 'function showLoading(){';
        $s .= 'clearResults();showBox();';
        $s .= 'const div=document.createElement("div");';
        $s .= 'div.className="list-group-item text-center";';
        $s .= 'div.innerHTML=\'<span class="spinner-border spinner-border-sm me-2"></span>در حال جستجو...\';';
        $s .= 'resultsBox.appendChild(div);';
        $s .= '}';

        $s .= 'function showEmpty(){';
        $s .= 'clearResults();showBox();';
        $s .= 'const div=document.createElement("div");';
        $s .= 'div.className="list-group-item text-muted small";';
        $s .= 'div.textContent="نتیجه‌ای یافت نشد";';
        $s .= 'resultsBox.appendChild(div);';
        $s .= '}';

        $s .= 'function selectItem(item){hiddenInput.value=item.id ?? "";searchInput.value=item.label ?? "";clearResults();}';

        $s .= 'function buildUrl(params){try{const url=new URL(config.endpoint, window.location.origin);Object.keys(params).forEach(function(key){if(params[key]!==undefined && params[key]!==null && params[key]!=="" ){url.searchParams.set(key, params[key]);}});if(config.limit && params.term){url.searchParams.set("limit", config.limit);}return url.toString();}catch(e){return null;}}';

        $s .= 'function fetchResults(params, isInitial){';
        $s .= 'const url=buildUrl(params);if(!url){return;}';
        $s .= 'if(state.controller){state.controller.abort();}';
        $s .= 'state.controller=new AbortController();';
        $s .= 'state.loading=true;';
        $s .= 'if(!isInitial){showLoading();}';
        $s .= 'fetch(url,{headers:{"Accept":"application/json"},signal:state.controller.signal})';
        $s .= '.then(function(response){return response.json ? response.json() : [];})';
        $s .= '.then(function(data){';
        $s .= 'state.loading=false;';
        $s .= 'if(!Array.isArray(data)){return;}';
        $s .= 'if(isInitial){';
        $s .= 'if(hiddenInput.value){const exact=data.find(item=>item.id==hiddenInput.value);if(exact){selectItem(exact);}}';
        $s .= 'return;}';
        $s .= 'const items=config.limit?data.slice(0, config.limit):data;';
        $s .= 'if(!items.length){showEmpty();return;}';
        $s .= 'renderResults(items);';
        $s .= '})';
        $s .= '.catch(function(error){if(error.name!=="AbortError"){console.error(error);} });';
        $s .= '}';

        $s .= 'function renderResults(items){';
        $s .= 'clearResults();showBox();';
        $s .= 'items.forEach(function(item){';
        $s .= 'const option=document.createElement("button");';
        $s .= 'option.setAttribute("type","button");';
        $s .= 'option.className="list-group-item list-group-item-action";';
        $s .= 'option.textContent=item.label ?? item.id;';
        $s .= 'option.addEventListener("click",function(){selectItem(item);});';
        $s .= 'resultsBox.appendChild(option);';
        $s .= '});';
        $s .= '}';

        $s .= 'function handleInput(){';
        $s .= 'const term=searchInput.value.trim();';
        $s .= 'if(term.length < config.minChars){showHint();return;}';
        $s .= 'if(state.timer){clearTimeout(state.timer);}';
        $s .= 'state.timer=setTimeout(function(){fetchResults({term:term});},300);';
        $s .= '}';

        $s .= 'searchInput.addEventListener("focus", function(){';
        $s .= 'const term=searchInput.value.trim();';
        $s .= 'if(!term){showHint();}';
        $s .= 'else if(term.length < config.minChars){showHint();}';
        $s .= 'else{handleInput();}';
        $s .= '});';

        $s .= 'searchInput.addEventListener("input", handleInput);';

        $s .= 'document.addEventListener("click", function(event){if(!container.contains(event.target)){clearResults();}});';

        $s .= 'if(hiddenInput.value){';
        $s .= 'if(config.initialLabel){selectItem({id:hiddenInput.value,label:config.initialLabel});}';
        $s .= 'else if(config.endpoint){fetchResults({id:hiddenInput.value}, true);}';
        $s .= '}';

        $s .= 'const api={getValue:function(){return hiddenInput.value;},setValue:function(item){if(item && typeof item==="object" && item.id!==undefined && item.label!==undefined){selectItem(item);}else if(item===null || item===undefined){selectItem({id:"",label:""});}}};';
        $s .= 'container.searchableField = api;';
        $s .= 'window.formBuilderSearchableFields[config.fieldName]=api;';
        $s .= '})();</script>';


        return $s;
    }

    public function getValue(): mixed
    {
        return $this->attributes['value'] ?? null;
    }

    public function setValue($value): void
    {
        $this->attributes['value'] = $value;
    }
}

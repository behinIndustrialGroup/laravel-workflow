<?php

namespace MyFormBuilder\Fields;

class LocationField extends AbstractField
{
    public function render(): string
    {
        $s = '<div class="form-group">';
        $s .= '<label>';
        $s .= trans('fields.' . $this->name);
        if ($this->attributes['required'] == 'on' && $this->attributes['readonly'] != 'on') {
            $s .= ' <span class="text-danger">*</span>';
        }
        $s .= '</label>';
        $s .= '<div id="' . $this->name . '" style="width: 100%; height: 300px;"></div>';
        $s .= '<script>';
        if(isset($this->attributes['defaultLat']) && isset($this->attributes['defaultLng'])){
            $s .= '$(document).ready(function() {
                var app = new Mapp({
                    element: "#' . $this->name . '",
                    presets: {
                    latlng: {
                        lat: ' . $this->attributes['defaultLat'] . ',
                        lng: ' . $this->attributes['defaultLng'] . '
                    },
                    zoom: ' . $this->attributes['defaultZoom'] . '
                    },
                    apiKey: "' . env('MAP_API_KEY') . '"
                });
                app.addLayers();
                app.addMarker({
                    name: "advanced-marker",
                    latlng: {
                        lat: ' . $this->attributes['defaultLat'] . ',
                        lng: ' . $this->attributes['defaultLng'] . '
                    },
                    popup: false
                });
                app.map.on("click", function(e) {
                    // آدرس یابی و نمایش نتیجه در یک باکس مشخص
                    // app.showReverseGeocode({
                    //     state: {
                    //         latlng: {
                    //             lat: e.latlng.lat,
                    //             lng: e.latlng.lng
                    //         },
                    //         zoom: 16
                    //     }
                    // });

                    app.addMarker({
                        name: "advanced-marker",
                        latlng: {
                            lat: e.latlng.lat,
                            lng: e.latlng.lng
                        },
                        icon: app.icons.red,
                        popup: false
                    });
                    $("#' . $this->name . '_lat").val(e.latlng.lat);
                    $("#' . $this->name . '_lng").val(e.latlng.lng);

                    // برای سفارشی سازی نمایش نتیجه به جای متد بالا از متد زیر میتوان استفاده کرد


                });
                });';
        }else{
            $s .= '$(document).ready(function() {
                var app = new Mapp({
                    element: "#' . $this->name . '",
                    presets: {
                    latlng: {
                        lat: 35.73249,
                        lng: 51.42268
                    },
                    zoom: ' . $this->attributes['defaultZoom'] . '
                    },
                    apiKey: "' . env('MAP_API_KEY') . '"
                });
                app.addLayers();
                app.map.on("click", function(e) {
                    // آدرس یابی و نمایش نتیجه در یک باکس مشخص
                    // app.showReverseGeocode({
                    //     state: {
                    //         latlng: {
                    //             lat: e.latlng.lat,
                    //             lng: e.latlng.lng
                    //         },
                    //         zoom: 16
                    //     }
                    // });

                    app.addMarker({
                        name: "advanced-marker",
                        latlng: {
                            lat: e.latlng.lat,
                            lng: e.latlng.lng
                        },
                        icon: app.icons.red,
                        popup: false
                    });
                    $("#' . $this->name . '_lat").val(e.latlng.lat);
                    $("#' . $this->name . '_lng").val(e.latlng.lng);

                    // برای سفارشی سازی نمایش نتیجه به جای متد بالا از متد زیر میتوان استفاده کرد


                });
                });';
        }


        $s .= '</script>';
        $s .= '<input type="hidden" name="' .$this->name.  '_lng" id="' .$this->name.  '_lng" value="'.$this->attributes['defaultLng'].'" >';
        $s .= '<input type="hidden" name="' .$this->name.  '_lat" id="' .$this->name.  '_lat" value="'.$this->attributes['defaultLat'].'" >';
        $s .= '</div>';

        return $s;
        if (!isset($this->attributes['type'])) {
            $this->attributes['type'] = 'text';
        }
        return sprintf('<input %s>', $this->buildAttributes());
    }
}

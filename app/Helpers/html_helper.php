<?php
    function handledata($data = null) {
        if (is_null($data)) return false;
        return base64_encode(json_encode($data));
    }

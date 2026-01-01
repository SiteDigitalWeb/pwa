@extends ('adminsite.layout')

@section('title', 'Dashboard')

@section('ContenidoSite-01')

<div class="row">
<div class="col-sm-6">
 <a href="{{ route('admin.pwa.index') }}" class="widget widget-hover-effect1">
  <div class="widget-simple">
   <div class="widget-icon pull-left themed-background animation-fadeIn">
    <i class="gi gi-wallet"></i>
   </div>
   <div class="pull-right">
    <span id="mini-chart-sales"><canvas width="190" height="64" style="display: inline-block; width: 190px; height: 64px; vertical-align: top;"></canvas></span>
   </div>
   <h3 class="widget-content animation-pullDown visible-lg">
     ⚙️ Configuración <strong>PWA</strong>
    <small>Administación PWA</small>
    </h3>
   </div>
  </a>
</div>

<div class="col-sm-6">
 <a href="{{ route('manifest.json') }}" target="_blank" class="widget widget-hover-effect1">
  <div class="widget-simple">
   <div class="widget-icon pull-left themed-background animation-fadeIn">
    <i class="gi gi-crown"></i>
   </div>
   <div class="pull-right">
    <span id="mini-chart-brand"><canvas width="176" height="64" style="display: inline-block; width: 176px; height: 64px; vertical-align: top;"></canvas></span>
   </div>
   <h3 class="widget-content animation-pullDown visible-lg">
    📄 Manifest <strong> Actual</strong>
    <small>Manifest.json</small>
   </h3>
  </div>
 </a>
 </div>

</div>

@endsection
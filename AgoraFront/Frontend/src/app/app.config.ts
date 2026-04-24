import {
  ApplicationConfig,
  provideBrowserGlobalErrorListeners,
  provideZoneChangeDetection,
  LOCALE_ID
} from '@angular/core';
import { provideRouter } from '@angular/router';
import { provideHttpClient, withInterceptors, withXsrfConfiguration } from '@angular/common/http';
import { routes } from './app.routes';
import { authInterceptor } from './interceptors/auth.interceptor';
import { registerLocaleData } from '@angular/common';
import localeFr from '@angular/common/locales/fr';

// Register French locale
registerLocaleData(localeFr, 'fr-FR');

// 1. Importation des providers de ng2-charts
import { provideCharts, withDefaultRegisterables } from 'ng2-charts';

export const appConfig: ApplicationConfig = {
  providers: [
    provideBrowserGlobalErrorListeners(),
    provideZoneChangeDetection({ eventCoalescing: true }),
    provideRouter(routes),

    // Set French as default locale
    { provide: LOCALE_ID, useValue: 'fr-FR' },

    // 2. Configuration globale pour Chart.js (indispensable pour ng2-charts 5+)
    provideCharts(withDefaultRegisterables()),

    provideHttpClient(
      // On active l'interceptor pour injecter le token Bearer
      withInterceptors([authInterceptor]),

      // On garde ta configuration XSRF pour Laravel
      withXsrfConfiguration({
        cookieName: 'XSRF-TOKEN',
        headerName: 'X-XSRF-TOKEN',
      })
    )
  ]
};

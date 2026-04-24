import { HttpInterceptorFn } from '@angular/common/http';
import { inject } from '@angular/core';
import { AuthService } from '../services/auth.service';

export const authInterceptor: HttpInterceptorFn = (req, next) => {
  const authService = inject(AuthService);
  const token = authService.getToken();

  // Clone de la requête pour ajouter les headers
  const authReq = token
    ? req.clone({
        setHeaders: {
          Authorization: `Bearer ${token}`,
          Accept: 'application/json'  // Très important pour éviter HTML
        }
      })
    : req.clone({
        setHeaders: {
          Accept: 'application/json' // Toujours demander JSON même sans token
        }
      });

  return next(authReq);
};

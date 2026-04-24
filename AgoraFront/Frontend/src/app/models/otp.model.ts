export interface OtpRequest {
  email: string;
}

export interface OtpVerify {
  email: string;
  otp: string;
}

export interface PasswordReset {
  email: string;
  password: string;
  password_confirmation: string;
}

export interface ApiResponse {
  success: boolean;
  message: string;
  error?: string;
}
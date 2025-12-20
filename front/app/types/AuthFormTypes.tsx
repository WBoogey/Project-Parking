export type AuthFormMode = "signup" | "login" | "signup-pro";

export interface AuthFormData {
  firstName?: string;
  lastName?: string;
  email: string;
  password: string;
  companyName?: string;
  address?: string;
  city?: string;
  role?: string;
}

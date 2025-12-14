export type ButtonSize = "sm" | "md" | "lg" | "full";

export type ButtonVariant = "default" | "plain" | "outline";

export type TextInputVariant = "md" | "full";

export type SelectVariantType = "sm" | "md" | "lg" | "full";

export interface RadioOption {
  value: string;
  label: string;
}

export interface EstimationFormData {
  parkingStatus: string;
  spotType: string;
  address: string;
  email: string;
}

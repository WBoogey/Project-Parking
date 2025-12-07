import type { EstimationFormData, RadioOption } from "@/types/ComponentTypes";
import type { FormEvent } from "react";
import { useState } from "react";
import Button from "../atoms/Button";
import InputComplete from "../molecules/InputComplete";
import RadioGroup from "../molecules/RadioGroup";
import SelectInput from "../molecules/SelectInput/SelectInput";

type SubmitHandler = (formValues: EstimationFormData) => void;

interface EstimationFormProps {
  onSubmit: SubmitHandler;
}

const parkingStatusOptions: RadioOption[] = [
  { value: "free", label: "Libre" },
  { value: "rented", label: "Déjà louée" },
  { value: "acquisition", label: "Je me renseigne pour une acquisition" },
];

const spotTypeChoices = ["Place de voiture", "Place de moto"];

const EstimationForm = ({ onSubmit }: EstimationFormProps) => {
  const [formData, setFormData] = useState<EstimationFormData>({
    parkingStatus: "",
    spotType: "",
    address: "",
    email: "",
  });

  const handleChange = (field: keyof EstimationFormData, value: string) => {
    setFormData((prev) => ({ ...prev, [field]: value }));
  };

  const handleSubmit = (e: FormEvent) => {
    e.preventDefault();
    onSubmit(formData);
  };

  return (
    <form
      onSubmit={handleSubmit}
      className="flex flex-col gap-6 p-8 bg-primary border border-tertiary rounded-3xl max-w-3xl"
      data-testid="estimation-form"
    >
      <RadioGroup
        name="parkingStatus"
        label="Votre place de parking est actuellement :"
        options={parkingStatusOptions}
        value={formData.parkingStatus}
        onChange={(value) => handleChange("parkingStatus", value)}
        required
      />

      <div className="flex flex-col gap-1.5">
        <label className="font-semibold text-sm font-inter text-secondary">
          Type de place*
        </label>
        <SelectInput
          placeholder="Type de place"
          choices={spotTypeChoices}
          variant="full"
          value={formData.spotType}
          onChange={(value) => handleChange("spotType", value)}
        />
      </div>

      <InputComplete
        id="address"
        label="Adresse de votre parking*"
        placeholder="55 Rue du Faubourg Saint-Honoré, 75008 Paris"
        variant="full"
        value={formData.address}
        onChange={(e) => handleChange("address", e.target.value)}
        required
      />

      <InputComplete
        id="email"
        label="Votre email*"
        placeholder="Votre email"
        type="email"
        variant="full"
        value={formData.email}
        onChange={(e) => handleChange("email", e.target.value)}
        required
      />

      <Button type="submit" size="full" onClick={() => {}}>
        Obtenir une estimation
      </Button>
    </form>
  );
};

export default EstimationForm;


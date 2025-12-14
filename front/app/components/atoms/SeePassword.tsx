import type { ButtonHTMLAttributes, Dispatch, SetStateAction } from "react";
import { cn } from "cn-utility";
import { PopiconsEyeLine, PopiconsEyeOffLine } from "@popicons/react";

interface SeePasswordProps extends ButtonHTMLAttributes<HTMLButtonElement> {
  visible: boolean;
  setVisible: Dispatch<SetStateAction<boolean>>;
  className?: string;
}

const SeePassword = ({
  visible,
  setVisible,
  className,
  ...props
}: SeePasswordProps) => {
  const Icon = visible ? PopiconsEyeLine : PopiconsEyeOffLine;
  const label = visible
    ? "Masquer le mot de passe"
    : "Afficher le mot de passe";

  return (
    <button
      onClick={() => setVisible(!visible)}
      className={cn("px-3 py-2 cursor-pointer", className)}
      aria-label={label}
      {...props}
    >
      <Icon className="text-accent" />
    </button>
  );
};

export default SeePassword;

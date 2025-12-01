import type { ButtonHTMLAttributes, Dispatch, SetStateAction } from "react";
import { Eye, EyeOff } from "lucide-react";
import { cn } from "cn-utility";

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
  const Icon = visible ? Eye : EyeOff;

  return (
    <button
      onClick={() => setVisible(!visible)}
      className={cn("px-3 py-2", className)}
      {...props}
    >
      <Icon className="text-accent" />
    </button>
  );
};

export default SeePassword;
